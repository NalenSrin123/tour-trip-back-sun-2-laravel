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
    ) {}

    public function createPaymentForBooking(Booking $booking, array $paymentData): array
    {
        // Generate unique 20-character transaction ID matching ABA PayWay constraints
        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Phnom_Penh'));
        $tranId = $now->format('ymdHis') . mt_rand(1000, 9999);

        // 1. Create the pending payment record in DB
        $payment = Payment::create([
            'booking_id'     => $booking->id,
            'amount'         => $booking->total_price,
            'payment_method' => $paymentData['payment_method'] ?? 'aba_pay',
            'payment_status' => 'pending',
            'transaction_id' => $tranId,
            'payment_date'   => now(),
        ]);

        // 2. Resolve gateway and initiate payment request
        $gateway = $this->processor->resolve($paymentData['payment_method']);
        $gatewayResponse = $gateway->initiatePayment($payment, $booking, $paymentData);

        return [
            'payment'          => $payment,
            'gateway_response' => $gatewayResponse,
        ];
    }
}