<?php

namespace App\Services\Payments;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class PaystackService
{
    public function initializeTransaction(array $payload): array
    {
        return $this->post('/transaction/initialize', $payload);
    }

    public function verifyTransaction(string $reference): array
    {
        return $this->get('/transaction/verify/' . urlencode($reference));
    }

    protected function get(string $path): array
    {
        $response = $this->client()->get($this->endpoint($path));

        if ($response->failed()) {
            $message = Arr::get($response->json(), 'message', 'Paystack request failed.');
            throw new \RuntimeException($message);
        }

        return $response->json();
    }

    protected function post(string $path, array $payload): array
    {
        try {
            $response = $this->client()->post($this->endpoint($path), $payload);
        } catch (ConnectionException $exception) {
            throw new \RuntimeException('Unable to reach Paystack. Please try again later.', 0, $exception);
        }

        if ($response->failed()) {
            $message = Arr::get($response->json(), 'message', 'Paystack request failed.');
            throw new \RuntimeException($message);
        }

        return $response->json();
    }

    protected function client()
    {
        $secretKey = Config::get('paystack.secret_key');

        if (! $secretKey) {
            throw new \RuntimeException('Paystack secret key is not configured.');
        }

        return Http::withToken($secretKey)
            ->acceptJson()
            ->timeout(15);
    }

    protected function endpoint(string $path): string
    {
        $baseUrl = rtrim(Config::get('paystack.base_url', 'https://api.paystack.co'), '/');

        return $baseUrl . '/' . ltrim($path, '/');
    }
}
