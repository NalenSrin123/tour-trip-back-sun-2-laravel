<?php
namespace App\Services\Controller;


use App\Contracts\PaymentGatewayInterface;
use App\Services\Gateways\ABAPaywayService;
use InvalidArgumentException;

class PaymentProcessorService
{
    public function resolve(string $paymentMethod): PaymentGatewayInterface
    {
        return match ($paymentMethod) {
            'aba_pay' => app(ABAPaywayService::class),
            // 'bakong'  => app(BakongService::class), // Plug in later
            default   => throw new InvalidArgumentException("Payment method [{$paymentMethod}] is not supported."),
        };
    }
}