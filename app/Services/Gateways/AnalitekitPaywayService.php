<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Booking;
use App\Models\Payment;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\Http;

class AnalitekitPaywayService implements PaymentGatewayInterface
{
    protected string $apiKey;
    protected string $baseUrl;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->apiKey = env('ANALITEKIT_API_KEY');
        $this->baseUrl = env('ANALITEKIT_BASE_URL');
    }
    public function initiatePayment(Payment $payment, Booking $booking, array $options = []): array
    {
        $timezone = new DateTimeZone('Asia/Phnom_Penh');
        $reqTime = (new DateTimeImmutable('now', $timezone))->format('Ymd');
        // Implementation for initiating payment
        $amount = number_format((float) $payment->amount, 2, '.', '');
        $currency = (string) ($options['currency'] ?? 'USD');
        $description = $payment->description ?? ('Booking #' . $reqTime . "-" . $booking->id);
        // Use transaction_id already created on the Payment model
        $orderId = (string) $payment->transaction_id;

        // Sending application/json request to Analitekit API
        $response = Http::acceptJson()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
            ->post($this->baseUrl . '/v1/payments/qr', [
                'amount' => (float) $amount,
                'currency' => $currency,
                'description' => $description,
                'orderId' => $orderId,
            ]);

        $data = $response->json() ?? [];
        // Return the payment details
        return [
            // Analitekit returns its transaction ID under 'id' or 'data.id'
            'remote_id' => $data['data']['id'] ?? $data['id'] ?? null,
            'response' => $data,
        ];
    }

    public function checkTransaction(string $tranId): array
    {
        $cleanId = trim($tranId);

        // Ensure baseUrl does not have a trailing slash
        $base = rtrim($this->baseUrl, '/');

        // Make sure /api is included if your base URL doesn't have it
        $endpoint = str_contains($base, '/api')
            ? "{$base}/v1/payments/status/{$cleanId}"
            : "{$base}/api/v1/payments/status/{$cleanId}";

        $response = Http::acceptJson()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
            ->get($endpoint);

        if (!$response->successful()) {
            \Log::warning("Analitekit checkTransaction failed: HTTP {$response->status()}", [
                'url' => $endpoint,
                'body' => $response->body(),
            ]);
        }

        return $response->json() ?? [];
    }

}
