<?php

namespace App\Services\Notification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsNotificationService
{
    private ?string $apiKey;

    private ?string $senderId;

    private ?string $endpoint;

    public function __construct(
        ?string $apiKey = null,
        ?string $senderId = null,
        ?string $endpoint = null,
    ) {
        $config = (array) config('services.sms');

        $this->apiKey = $apiKey ?? ($config['key'] ?? null);
        $this->senderId = $senderId ?? ($config['sender'] ?? null);
        $this->endpoint = $endpoint ?? ($config['endpoint'] ?? null);
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey) && filled($this->senderId) && filled($this->endpoint);
    }

    /**
     * @param  array<int, string>  $recipients
     */
    public function sendBulk(array $recipients, string $message): void
    {
        if (! $this->isConfigured()) {
            Log::warning('SMS dispatch skipped due to missing configuration', [
                'has_api_key' => filled($this->apiKey),
                'has_sender_id' => filled($this->senderId),
                'has_endpoint' => filled($this->endpoint),
            ]);

            return;
        }

        $sanitized = collect($recipients)
            ->map(static fn (string $number): string => preg_replace('/\s+/', '', $number) ?? $number)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($sanitized) || $message === '') {
            return;
        }

        foreach (array_chunk($sanitized, 100) as $batch) {
            try {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'api-key' => $this->apiKey,
                ])->post($this->endpoint, [
                    'sender' => $this->senderId,
                    'message' => $message,
                    'recipients' => $batch,
                ]);

                if ($response->failed()) {
                    Log::warning('SMS dispatch failed', [
                        'endpoint' => $this->endpoint,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Throwable $exception) {
                Log::error('SMS dispatch exception', [
                    'endpoint' => $this->endpoint,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}
