<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaywayService
{
    protected string $merchantId;
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->merchantId = (string) config('services.aba_payway.merchant_id', '');
        $this->apiKey = (string) config('services.aba_payway.api_key', '');
        $this->baseUrl = (string) config('services.aba_payway.base_url', 'https://checkout-sandbox.payway.com.kh');

        if (empty($this->merchantId) || empty($this->apiKey)) {
            throw new \Exception('ABA PayWay Merchant ID or API Key is missing. Check your .env file and run "php artisan config:clear".');
        }
    }

    /**
     * Compute HMAC-SHA512 hash and return Base64 encoded string
     */
    public function generateHash(string $rawString): string
    {
        return base64_encode(hash_hmac('sha512', $rawString, $this->apiKey, true));
    }

    /**
     * Initiate checkout purchase request
     */
    public function purchase(array $data)
    {
        // 1. UTC Timestamp: strictly YYYYMMDDHHmmss
        $reqTime = gmdate('YmdHis');

        // 2. Unique tran_id (max 20 chars)
        $tranId = date('ymdHis') . mt_rand(1000, 9999);

        // 3. Normalized parameters
        $amount = number_format((float) ($data['amount'] ?? 1.00), 2, '.', '');
        $firstName = (string) ($data['firstname'] ?? 'Dara');
        $lastName = (string) ($data['lastname'] ?? 'Sok');
        $phone = (string) ($data['phone'] ?? '012345678');
        $email = (string) ($data['email'] ?? 'dara@example.com');
        $returnUrl = base64_encode($data['return_url'] ?? 'https://yourwebsite.com/callback');

        // 4. Concatenate strictly in PayWay's expected sequence
        $rawHash = $reqTime . $this->merchantId . $tranId . $amount . $firstName . $lastName . $email . $phone . $returnUrl;

        // 5. Generate HMAC-SHA512 binary hash and base64-encode it
        $hash = base64_encode(hash_hmac('sha512', $rawHash, $this->apiKey, true));

        // 6. Request payload MUST match the hashed fields exactly
        $payload = [
            'req_time' => $reqTime,
            'merchant_id' => $this->merchantId,
            'tran_id' => $tranId,
            'amount' => $amount,
            'firstname' => $firstName,
            'lastname' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'return_url' => $returnUrl,
            'hash' => $hash,
        ];

        $response = Http::asMultipart()->post("{$this->baseUrl}/api/payment-gateway/v1/payments/purchase", $payload);

        return [
            'tran_id' => $tranId,
            'response' => $response->json(),
        ];
    }

    /**
 * Check transaction status from ABA PayWay
 */
public function checkTransaction(string $tranId)
{
    $reqTime = gmdate('YmdHis');
    
    // Hash sequence for check-transaction: req_time + merchant_id + tran_id
    $rawHash = $reqTime . $this->merchantId . $tranId;
    $hash = base64_encode(hash_hmac('sha512', $rawHash, $this->apiKey, true));

    $response = Http::asMultipart()->post("{$this->baseUrl}/api/payment-gateway/v1/payments/check-transaction", [
        'req_time'    => $reqTime,
        'merchant_id' => $this->merchantId,
        'tran_id'     => $tranId,
        'hash'        => $hash,
    ]);

    return $response->json();
}
}