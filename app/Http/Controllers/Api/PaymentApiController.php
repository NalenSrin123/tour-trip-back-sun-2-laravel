<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ServiceController\ProcessPaymentService;
use App\Services\PaywayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentApiController extends Controller
{
    protected $paymentService;
    public function __construct(ProcessPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;

    }
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'lastname' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            // Call the exact same shared service
            $data = $this->paymentService->processCheckout($validated);

            // Return JSON payload for the API Client
            return response()->json([
                'success' => true,
                'checkout_data' => $data
            ], 200);

        } catch (\Exception $e) {
            // Handle error specifically for API
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function callback(Request $request)
    {
        // Handle redirect back from PayWay after user pays
        $tranId = $request->query('tran_id');
        $status = $request->query('status');

        return response()->json([
            'message' => 'Payment processed',
            'tran_id' => $tranId,
            'status' => $status,
        ]);
    }
    public function checkStatus(string $tran_id, PaywayService $payway)
    {
        $result = $payway->checkTransaction($tran_id);

        // PayWay returns status code "00" or 0 when the user has completed payment
        $statusCode = $result['status']['code'] ?? null;

        if ($statusCode === '00' || $statusCode === 0 || $statusCode === '0') {
            // Optional: Update your order record in DB to 'PAID' here

            return response()->json([
                'status' => 0,
                'message' => 'Paid successfully',
                'data' => $result,
            ]);
        }

        return response()->json([
            'status' => $statusCode ?? 1,
            'message' => 'Pending payment',
        ]);
    }

    public function success(Request $request)
    {
        $tranId = $request->query('tran_id');

        return view('payway.success', compact('tranId'));
    }
}
