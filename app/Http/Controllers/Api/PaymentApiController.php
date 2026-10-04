<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ServiceController\ProcessPaymentService;
use App\Models\Booking;
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
            'booking_id' => ['required','max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'lastname' => ['nullable', 'string', 'max:50'],
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);
        $validated['amount'] = $booking->total_price; // Use total_price for API checkout

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
        $validated = $request->validate([
            'tran_id' => ['required', 'string'],
            'status' => ['required', 'string'],
        ]);
        // Handle redirect back from PayWay after user pays
        $tranId = $validated['tran_id'];
        $status = $validated['status'];
        return response()->json([
            'message' => 'Payment processed',
            'tran_id' => $tranId,
            'status' => $status,
        ]);
    }
    public function checkPaymentStatus(Request $request, ProcessPaymentService $payway)
    {
        $validated = $request->validate([
            'tran_id' => ['required', 'string'],
        ]);
        $statusData = $payway->verifyTransaction($validated['tran_id']);

        if ($statusData['is_paid']) {
            return response()->json([
                'status' => 0,
                'message' => 'Paid successfully',
                'data' => $statusData['data'],
            ]);
        }


        return response()->json([
            'message' => 'Check Payment status.',
            'data' => $statusData['data'],
        ]);
    }

}
