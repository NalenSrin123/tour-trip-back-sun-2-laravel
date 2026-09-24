<?php

namespace App\Http\Controllers\ServiceController;

use App\Http\Controllers\Controller;
use App\Services\PaywayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\CountValidator\Exception;

class ProcessPaymentService extends Controller
{
    protected $payway;
    public function __construct(PaywayService $payway)
    {

        $this->payway = $payway;
    }
    public function processCheckout(array $validatedData)
    {
        $user = Auth::user();
        $firstname = $user ? $user->name : 'Customer';
        $lastname = $validatedData['lastname'] ?? 'User';
        $phone = $validatedData['phone'] ?? ($user->phone ?? '012345678');
        $email = $validatedData['email'] ?? ($user->email ?? 'customer@example.com');

        // Request PayWay Purchase
        $result = $this->payway->purchase([
            'amount' => $validatedData['amount'],
            'firstname' => $firstname,
            'lastname' => $lastname,
            'phone' => $phone,
            'email' => $email,
            'return_url' => route('payment.callback'),
        ]);

        $response = $result['response'] ?? [];
        $statusCode = $response['status']['code'] ?? null;

        // Guard against API errors by throwing an exception
        if ($statusCode !== '00' && $statusCode !== 0 && $statusCode !== '0') {
            $errorMessage = $response['status']['message'] ?? 'Unable to initialize payment with PayWay.';
            throw new Exception($errorMessage);
        }

        // Return the required data back to the controller
        return [
            'tran_id' => $result['tran_id'] ?? null,
            'response' => $response,
        ];
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
    public function verifyTransaction(string $tran_id)
    {
        $result = $this->payway->checkTransaction($tran_id);
        $statusCode = $result['status']['code'] ?? null;

        $isPaid = ($statusCode === '00' || $statusCode === 0 || $statusCode === '0');

        if ($isPaid) {
            // Update your order record in DB to 'PAID' here
            // e.g., Order::where('tran_id', $tran_id)->update(['status' => 'paid']);
        }

        return [
            'is_paid' => $isPaid,
            'status_code' => $statusCode,
            'data' => $result,
        ];
    }

    public function success(Request $request)
    {
        $tranId = $request->query('tran_id');

        return view('payway.success', compact('tranId'));
    }
}
