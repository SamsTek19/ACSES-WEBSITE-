<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class RushPayService
{
    /**
     * Initialize a payment transaction.
     */
    public function initializeTransaction(array $payload): array
    {
        return $this->post('/payments/create', $payload);
    }

    /**
     * Generate a widget session for a payment.
     */
    public function generateWidgetSession(string $reference): array
    {
        return $this->post('/payments/widget-session', [
            'payment_reference' => $reference
        ]);
    }

    /**
     * Verify a payment transaction.
     */
    public function verifyTransaction(string $reference): array
    {
        return $this->get('/payments/' . urlencode($reference));
    }

    /**
     * Build the authenticated HTTP client.
     *
     * Prefers the simpler X-API-Key header when RUSHPAY_API_KEY is set.
     * Falls back to OAuth client-credentials flow otherwise.
     */
    protected function client()
    {
        $apiKey = trim((string) Config::get('rushpay.api_key'));

        if ($apiKey !== '') {
            return Http::withHeaders(['X-API-Key' => $apiKey])
                ->acceptJson()
                ->timeout(15);
        }

        return Http::withToken($this->getOAuthAccessToken())
            ->acceptJson()
            ->timeout(15);
    }

    /**
     * Make a GET request to RushPay.
     */
    protected function get(string $path): array
    {
        try {
            $response = $this->client()->get($this->endpoint($path));

            if ($response->failed()) {
                $this->handleErrorResponse($response);
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RushPay GET request failed: ' . $e->getMessage(), ['path' => $path]);
            throw $e;
        }
    }

    /**
     * Make a POST request to RushPay.
     */
    protected function post(string $path, array $payload): array
    {
        try {
            $response = $this->client()->post($this->endpoint($path), $payload);

            if ($response->failed()) {
                $this->handleErrorResponse($response);
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RushPay POST request failed: ' . $e->getMessage(), ['path' => $path, 'payload' => $payload]);
            throw $e;
        }
    }

    /**
     * Retrieve or refresh an OAuth access token via client-credentials grant.
     */
    protected function getOAuthAccessToken(): string
    {
        return Cache::remember('rushpay_access_token', now()->addMinutes(55), function () {
            $clientId = trim((string) Config::get('rushpay.client_key'));
            $clientSecret = trim((string) Config::get('rushpay.client_secret'));

            if ($clientId === '' || $clientSecret === '') {
                throw new \RuntimeException(
                    'RushPay credentials are not configured. '
                    . 'Set RUSHPAY_API_KEY for API-key auth, or set both RUSHPAY_CLIENT_KEY and RUSHPAY_CLIENT_SECRET for OAuth.'
                );
            }

            $response = Http::acceptJson()->post($this->endpoint('/auth/login'), [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'client_credentials',
            ]);

            if ($response->failed()) {
                Log::error('RushPay OAuth Authentication failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                    'debug_client_id' => $clientId,
                    'debug_secret_len' => strlen($clientSecret),
                ]);
                throw new \RuntimeException('Unable to authenticate with RushPay. Check RUSHPAY_CLIENT_KEY and RUSHPAY_CLIENT_SECRET.');
            }

            $token = Arr::get($response->json(), 'data.access_token')
                  ?? Arr::get($response->json(), 'access_token');

            if (!$token) {
                throw new \RuntimeException('RushPay authentication response did not contain an access token.');
            }

            return $token;
        });
    }

    /**
     * Handle error responses from RushPay.
     */
    protected function handleErrorResponse($response)
    {
        $data = $response->json();
        $message = Arr::get($data, 'message', 'RushPay request failed.');
        throw new \RuntimeException($message);
    }

    /**
     * Get the full endpoint URL.
     */
    protected function endpoint(string $path): string
    {
        $baseUrl = rtrim(Config::get('rushpay.base_url', 'https://api.rushpay.cash/v1'), '/');
        return $baseUrl . '/' . ltrim($path, '/');
    }
}
