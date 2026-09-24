<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ServiceController\ProcessPaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentProcess;
    public function __construct(ProcessPaymentService $paymentProcess)
    {
        $this->paymentProcess = $paymentProcess;
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
            // Call the shared service
            $data = $this->paymentProcess->processCheckout($validated);

            // Render Checkout View for Web
            return view('payway.checkout', [
                'tran_id' => $data['tran_id'],
                'response' => $data['response'],
            ]);

        } catch (\Exception $e) {
            // Handle error specifically for Web
            return back()->withErrors(['payway_error' => $e->getMessage()]);
        }
    }
    public function callback(Request $request)
    {
        $tranId = $request->query('tran_id');
        
        // Fixed: Redirect to the success view instead of returning JSON in a web controller
        return redirect()->route('payment.success', ['tran_id' => $tranId]);
    }
   public function checkStatus(string $tran_id)
    {
        $statusData = $this->paymentProcess->verifyTransaction($tran_id);

        if ($statusData['is_paid']) {
            return response()->json([
                'status' => 0,
                'message' => 'Paid successfully',
                'data' => $statusData['data'],
            ]);
        }

        return response()->json([
            'status' => $statusData['status_code'] ?? 1,
            'message' => 'Pending payment',
        ]);
    }

    public function success(Request $request)
    {
        $tranId = $request->query('tran_id');

        return view('payway.success', compact('tranId'));
    }
}