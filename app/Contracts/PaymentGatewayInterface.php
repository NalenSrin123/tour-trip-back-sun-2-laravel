<?php

namespace App\Contracts;

use App\Models\Booking;
use App\Models\Payment;

interface PaymentGatewayInterface
{
    public function initiatePayment(Payment $payment, Booking $booking, array $options = []): array;
    public function checkTransaction(string $transactionId): array;
}
