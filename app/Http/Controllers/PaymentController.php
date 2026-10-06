<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use App\Services\Controller\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentProcess;
    public function __construct(PaymentService $paymentProcess)
    {
        $this->paymentProcess = $paymentProcess;
    }

    public function checkPaymentStatus(string $tranId)
    {
        // Check the payment status using the PaymentService
        $statusResponse = $this->paymentProcess->checkPaymentStatus($tranId);

        return response()->json([
            'success' => true,
            'message' => 'Payment status retrieved successfully.',
            'data' => $statusResponse,
        ]);
    }
}