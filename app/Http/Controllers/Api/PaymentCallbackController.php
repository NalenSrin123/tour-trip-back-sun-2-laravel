<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Gateways\ABAPaywayService;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Str;

class PaymentCallbackController extends Controller
{
    public function handleAbaCallback(Request $request, ABAPaywayService $abaService)
    {
        // 1. Get tran_id sent by ABA PayWay
        $tranId = $request->input('tran_id');

        if (!$tranId) {
            return response()->json(['message' => 'Transaction ID missing'], 400);
        }

        // 2. Find the pending payment record
        $payment = Payment::where('transaction_id', $tranId)->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment record not found'], 404);
        }

        // Idempotency: Prevent duplicate execution if already marked paid
        if ($payment->payment_status === 'paid') {
            return response()->json(['message' => 'Payment already processed']);
        }

        // 3. Verify status directly with ABA PayWay API using checkTransaction()
        $gatewayStatus = $abaService->checkTransaction($tranId);

        // ABA returns status = 0 when transaction was successful
        if (isset($gatewayStatus['status']) && $gatewayStatus['status'] == 0) {
            DB::transaction(function () use ($payment, $gatewayStatus) {
                // Update payments table
                $payment->update([
                    'payment_status' => 'paid',
                    'bakong_md5' => $gatewayStatus['bakong_md5'] ?? $payment->bakong_md5,
                    'payment_date' => now(),
                ]);

                // Update bookings table
                $booking = $payment->booking;
                if ($booking) {
                    $booking->update(['status' => 'confirmed']);
                }

                // Generate invoice record
                Invoice::create([
                    'payment_id' => $payment->id,
                    'invoice_no' => 'INV-' . strtoupper(Str::random(8)),
                    'sub_total' => $payment->amount,
                    'tax_amount' => 0.00,
                    'total_amount' => $payment->amount,
                    'issued_at' => now(),
                ]);
            });

            return response()->json(['message' => 'Payment verified and settled successfully']);
        }

        return response()->json([
            'message' => 'Payment not verified or pending on gateway',
            'details' => $gatewayStatus,
        ], 422);
    }
    /**
     * Polling endpoint used by frontend waiting on KHQR screen
     */
    public function checkStatus(string $tranId, ABAPaywayService $abaService): JsonResponse
    {
        $payment = Payment::where('transaction_id', $tranId)->firstOrFail();

        // 1. If already updated by the webhook, return immediately
        if ($payment->payment_status === 'paid') {
            return response()->json([
                'status' => 'paid',
                'booking_id' => $payment->booking_id,
            ]);
        }

        // 2. Fallback check with ABA API
        $statusData = $abaService->checkTransaction($tranId);

        if (isset($statusData['status']) && (int) $statusData['status'] === 0) {
            $this->settlePayment($payment, $statusData);

            return response()->json([
                'status' => 'paid',
                'booking_id' => $payment->booking_id,
            ]);
        }

        return response()->json([
            'status' => 'pending',
            'details' => $statusData,
        ]);
    }

    /**
     * Settle payment, confirm booking, and create invoice
     */
    protected function settlePayment(Payment $payment, array $gatewayData): void
    {
        DB::transaction(function () use ($payment, $gatewayData) {
            $payment->update([
                'payment_status' => 'paid',
                'bakong_md5' => $gatewayData['bakong_md5'] ?? $payment->bakong_md5,
                'payment_date' => now(),
            ]);

            if ($payment->booking) {
                $payment->booking->update(['status' => 'confirmed']);
            }

            Invoice::create([
                'payment_id' => $payment->id,
                'invoice_no' => 'INV-' . strtoupper(Str::random(8)),
                'sub_total' => $payment->amount,
                'tax_amount' => 0.00,
                'total_amount' => $payment->amount,
                'issued_at' => now(),
            ]);
        });
    }
}
