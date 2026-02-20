<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FlutterwaveService
{
    private string $baseUrl = 'https://api.flutterwave.com/v3';
    private string $secretKey;
    private string $publicKey;
    private string $encryptionKey;

    public function __construct()
    {
        $this->secretKey = config('services.flutterwave.secret_key');
        $this->publicKey = config('services.flutterwave.public_key');
        $this->encryptionKey = config('services.flutterwave.encryption_key');
    }

    /**
     * Initialize a payment - returns payment link
     */
    public function initializePayment(array $data): array
    {
        $payload = [
            'tx_ref' => $data['tx_ref'] ?? $this->generateTxRef(),
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'NGN',
            'redirect_url' => $data['redirect_url'],
            'customer' => [
                'email' => $data['email'],
                'name' => $data['name'] ?? '',
            ],
            'customizations' => [
                'title' => 'Quizly Subscription',
                'description' => $data['description'] ?? 'Monthly subscription payment',
                'logo' => config('app.url') . '/logo.png',
            ],
            'meta' => $data['meta'] ?? [],
        ];

        try {
            $response = Http::withToken($this->secretKey)
                ->connectTimeout(15)
                ->timeout(10)
                ->post("{$this->baseUrl}/payments", $payload);

            if ($response->successful() && $response->json('status') === 'success') {
                return [
                    'success' => true,
                    'data' => $response->json('data'),
                    'tx_ref' => $payload['tx_ref'],
                ];
            }

            Log::error('Flutterwave payment init failed', [
                'response' => $response->json(),
                'payload' => array_merge($payload, ['customer' => ['email' => '***']])
            ]);

            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Payment initialization failed',
            ];
        } catch (\Exception $e) {
            Log::error('Flutterwave payment exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Payment service unavailable',
            ];
        }
    }

    /**
     * Verify transaction by ID (called from webhook)
     */
    public function verifyTransaction(string $transactionId): array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->connectTimeout(15)
                ->timeout(10)
                ->get("{$this->baseUrl}/transactions/{$transactionId}/verify");

            if ($response->successful() && $response->json('status') === 'success') {
                $data = $response->json('data');
                return [
                    'success' => true,
                    'status' => $data['status'],
                    'tx_ref' => $data['tx_ref'],
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'customer_email' => $data['customer']['email'] ?? null,
                    'flw_ref' => $data['flw_ref'],
                    'transaction_id' => $data['id'],
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Verification failed',
            ];
        } catch (\Exception $e) {
            Log::error('Flutterwave verify exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Verification service unavailable',
            ];
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(string $signature): bool
    {
        $secretHash = config('services.flutterwave.webhook_secret');
        return hash_equals($secretHash, $signature);
    }

    /**
     * Generate unique transaction reference
     */
    public function generateTxRef(): string
    {
        return 'QUIZLY-' . strtoupper(Str::random(16)) . '-' . time();
    }
}
