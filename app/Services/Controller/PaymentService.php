<?php
namespace App\Services\Controller;

use App\Models\Booking;
use App\Models\Payment;
use DateTimeImmutable;
use DateTimeZone;

class PaymentService
{
    public function __construct(
        protected PaymentProcessorService $processor
    ) {
    }

    public function createPaymentForBooking(Booking $booking, array $paymentData): array
    {
        // Generate unique 20-character transaction ID matching ABA PayWay constraints
        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Phnom_Penh'));
        $tranId = $now->format('ymdHis') . mt_rand(1000, 9999);

        // 1. Create the pending payment record in DB
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'payment_method' => $paymentData['payment_method'] ?? 'aba_pay',
            'payment_status' => 'pending',
            'transaction_id' => $tranId,
            'payment_date' => null,
        ]);

        // 2. Resolve gateway and initiate payment request
        $gateway = $this->processor->resolve($paymentData['payment_method']);
        $gatewayResponse = $gateway->initiatePayment($payment, $booking, $paymentData);


        // 3. If third-party returned their own transaction ID, sync it to the DB column
        $remoteId = $gatewayResponse['remote_id']
            ?? $gatewayResponse['response']['data']['id']
            ?? $gatewayResponse['response']['id']
            ?? null;

        if (!empty($remoteId)) {
            $payment->update([
                'transaction_id' => (string) $remoteId,
            ]);
        }
        return [
            'payment' => $payment,
            'gateway_response' => $gatewayResponse,
        ];
    }

    // app/Services/Controller/PaymentService.php

    public function checkPaymentStatus(Payment|string $paymentOrTranId): array
    {
        // If an instance of Payment was passed, use it directly
        if ($paymentOrTranId instanceof Payment) {
            $payment = $paymentOrTranId;
        } else {
            $cleanId = trim((string) $paymentOrTranId);

            $payment = Payment::where('transaction_id', $cleanId)->first();

            if (!$payment) {
                throw new \InvalidArgumentException("Payment not found for identifier: [{$cleanId}]");
            }
        }

        $gateway = $this->processor->resolve($payment->payment_method);
        return $gateway->checkTransaction((string) $payment->transaction_id);
    }
}