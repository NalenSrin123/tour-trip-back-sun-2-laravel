<?php
namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Payment;
use App\Models\Booking;
use Exception;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\Http;

class ABAPaywayService implements PaymentGatewayInterface
{
    protected string $merchantId;
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->merchantId = (string) config('services.aba_payway.merchant_id', '');
        $this->apiKey     = (string) config('services.aba_payway.api_key', '');
        $this->baseUrl    = rtrim((string) config('services.aba_payway.base_url', 'https://checkout-sandbox.payway.com.kh'), '/');

        if (empty($this->merchantId) || empty($this->apiKey)) {
            throw new Exception('ABA PayWay credentials missing in configuration.');
        }
    }

    public function generateHash(string $rawString): string
    {
        return base64_encode(hash_hmac('sha512', $rawString, $this->apiKey, true));
    }

    /**
     * Fulfills PaymentGatewayInterface by transforming Payment/Booking models into ABA payload
     */
    public function initiatePayment(Payment $payment, Booking $booking, array $options = []): array
    {
        $timezone = new DateTimeZone('Asia/Phnom_Penh');
        $reqTime  = (new DateTimeImmutable('now', $timezone))->format('YmdHis');

        $tranId    = (string) $payment->transaction_id;
        $amount    = number_format((float) $payment->amount, 2, '.', '');
        $firstName = (string) ($options['firstname'] ?? 'Guest');
        $lastName  = (string) ($options['lastname'] ?? 'User');
        $phone     = (string) ($options['phone'] ?? '012345678');
        $email     = (string) ($options['email'] ?? 'guest@example.com');
        $type      = 'purchase';
        $currency  = (string) ($options['currency'] ?? 'USD');

        $items = [
            [
                'name'     => 'Booking #' . $booking->id,
                'quantity' => '1',
                'price'    => $amount,
            ]
        ];
        $itemsBase64 = base64_encode(json_encode($items));

        $returnUrl          = base64_encode($options['return_url'] ?? route('aba.callback')); // The URL ABA will redirect to after payment
        $continueSuccessUrl = isset($options['continue_success_url']) ? base64_encode($options['continue_success_url']) : '';
        $returnDeeplink     = isset($options['return_deeplink']) ? base64_encode($options['return_deeplink']) : '';
        $paymentOption      = (string) ($options['payment_option'] ?? 'abapay_khqr');

        // ABA Hash formula
        $rawHash = $reqTime 
            . $this->merchantId 
            . $tranId 
            . $amount 
            . $itemsBase64 
            . '' // shipping
            . $firstName 
            . $lastName 
            . $email 
            . $phone 
            . $type 
            . $paymentOption 
            . $returnUrl 
            . '' // cancel_url
            . $continueSuccessUrl 
            . $returnDeeplink 
            . $currency;

        $hash = $this->generateHash($rawHash);

        $payload = [
            'req_time'             => $reqTime,
            'merchant_id'          => $this->merchantId,
            'tran_id'              => $tranId,
            'amount'               => $amount,
            'items'                => $itemsBase64,
            'firstname'            => $firstName,
            'lastname'             => $lastName,
            'email'                => $email,
            'phone'                => $phone,
            'type'                 => $type,
            'payment_option'       => $paymentOption,
            'return_url'           => $returnUrl,
            'continue_success_url' => $continueSuccessUrl,
            'return_deeplink'      => $returnDeeplink,
            'currency'             => $currency,
            'hash'                 => $hash,
        ];

        $response = Http::asMultipart()
            ->acceptJson()
            ->post("{$this->baseUrl}/api/payment-gateway/v1/payments/purchase", $payload);

        return [
            'tran_id'  => $tranId,
            'status'   => $response->status(),
            'response' => $response->json(),
        ];
    }

    public function checkTransaction(string $tranId): array
    {
        $reqTime = (new DateTimeImmutable('now', new DateTimeZone('Asia/Phnom_Penh')))->format('YmdHis');
        $rawHash = $reqTime . $this->merchantId . $tranId;
        $hash    = $this->generateHash($rawHash);

        $response = Http::asMultipart()
            ->acceptJson()
            ->post("{$this->baseUrl}/api/payment-gateway/v1/payments/check-transaction", [
                'req_time'    => $reqTime,
                'merchant_id' => $this->merchantId,
                'tran_id'     => $tranId,
                'hash'        => $hash,
            ]);

        return $response->json() ?? [];
    }
}