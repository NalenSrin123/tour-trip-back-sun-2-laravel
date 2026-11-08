<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Controller\PaymentProcessorService;
use App\Services\Controller\PaymentService;
use App\Services\Gateways\ABAPaywayService;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Str;

class PaymentCallbackController extends Controller
{
    public function handleWebhook(
        Request $request,
        PaymentService $paymentService
    ): JsonResponse {
        Log::info('Payment Webhook Received', [
            'payload' => $request->all(),
        ]);

        // 1. Identify transaction
        $transactionId = $request->input('id')
            ?? $request->input('transaction_id')
            ?? $request->input('tran_id')

            ?? $request->input('data.transaction_id')
            ?? $request->input('data.tran_id')
            ?? $request->input('data.id');


        if (!$transactionId) {
            return response()->json([
                'message' => 'Transaction identifier missing',
            ], 400);
        }

        // 2. Find local payment
        $payment = Payment::where('transaction_id', $transactionId)
            ->first();

        if (!$payment) {
            return response()->json([
                'message' => 'Payment not found',
            ], 404);
        }

        // 3. Already settled
        if ($payment->payment_status === 'paid') {
            return response()->json([
                'message' => 'Payment already processed',
            ], 200);
        }

        // 4. Verify payment with the actual gateway
        $gatewayStatus = $paymentService->checkPaymentStatus($payment);

        Log::info('Payment Gateway Status', [
            'transaction_id' => $transactionId,
            'payment_method' => $payment->payment_method,
            'gateway_response' => $gatewayStatus,
        ]);

        // 5. Determine whether payment is actually successful
        $isPaid = false;

        if ($payment->payment_method === 'bank_transfer') {

            // Analitekit / third-party gateway
            $status = strtolower(
                $gatewayStatus['data']['status']
                ?? $gatewayStatus['status']
                ?? ''
            );

            $isPaid = in_array($status, [
                'success',
                'paid',
                'completed',
                'approved',
            ]);

        } else {

            // ABA PayWay
            $isPaid = isset($gatewayStatus['status'])
                && (string) $gatewayStatus['status'] === '0';
        }

        // 6. Payment is not successful
        if (!$isPaid) {
            return response()->json([
                'message' => 'Payment not completed',
                'status' => $gatewayStatus['status'] ?? null,
            ], 200);
        }

        // 7. Settle payment
        $this->settlePayment(
            $payment,
            $gatewayStatus
        );

        return response()->json([
            'message' => 'Payment verified and settled successfully',
        ], 200);
    }
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
    public function checkStatus(string $tranId, PaymentService $paymentService): JsonResponse
    {
        $cleanId = trim($tranId);

        // 1. Find payment record to know which gateway to call
        $payment = Payment::where('transaction_id', $cleanId)
            ->first();

        if (!$payment) {
            return response()->json([
                'error' => 'Payment not found',
                'tran_id' => $cleanId,
            ], 404);
        }

        // 2. Fetch the raw response directly from the remote provider
        $remoteData = $paymentService->checkPaymentStatus($payment);

        // 3. (Optional) Still settle in the background if remote says it is completed/paid
        $isPaid = false;
        if ($payment->payment_method === 'bank_transfer') {
            $status = strtolower($remoteData['data']['status'] ?? $remoteData['status'] ?? '');
            $isPaid = in_array($status, ['completed', 'paid', 'approved', 'success']);
        } else {
            $isPaid = isset($remoteData['status']) && (string) $remoteData['status'] === '0';
        }

        if ($isPaid && !in_array(strtolower($payment->payment_status), ['paid', 'completed'])) {
            $this->settlePayment($payment, $remoteData);
        }

        // 4. Return the exact remote gateway JSON response
        return response()->json($remoteData);
    }

    /**
     * Settle payment, confirm booking, and create invoice
     */
    protected function settlePayment(Payment $payment, array $gatewayData): void
    {
        DB::transaction(function () use ($payment, $gatewayData) {
            $payment->update([
                'payment_status' => 'paid',
                // 'bakong_md5' => $gatewayData['bakong_md5'] ?? $payment->bakong_md5,
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
