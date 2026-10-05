<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use App\Services\Controller\ProcessPaymentService;
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
            'booking_id' => ['required','max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'lastname' => ['nullable', 'string', 'max:50'],
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);
        $validated['amount'] = $booking->total_price; // Use total_price for Web checkout
        try {
            // Call the shared service
            $data = $this->paymentProcess->processCheckout($validated);
            

            // Render Checkout View for Web
            return view('payway.checkout', [
                'tran_id' => $data['tran_id'],
                'response' => $data['response'],
                'amount' => $validated['amount'],
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