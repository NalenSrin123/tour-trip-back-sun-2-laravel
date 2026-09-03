<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Service\OTPService;
use App\Service\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AuthController extends Controller
{
    // Define a private property
    private $otpService;
    private $telegramService;

    public function __construct(OTPService $oTPService, TelegramService $telegramService)
    {
        $this->otpService = $oTPService;
        $this->telegramService = $telegramService;
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password_hash' => 'required|min:8|confirmed',
            'channel' => 'nullable|in:telegram,email', // Optional channel selection
        ]);
        $user = DB::transaction(function () use ($request) {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password_hash' => Hash::make($request->password_hash),
                // Note: email_verified_at is null by default
            ]);

            // Generate and send OTP based on the requested channel or default to Telegram
            $this->otpService->generateAndSend($user, $request->channel ?? 'telegram');

            return response()->json([
                'message' => 'User registered. Please check the Telegram group for your verification OTP.',
                'user' => $user,
            ], 201);
        });
    }


    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required|string|min:8'
            ]
        );

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        // Check if verified
        if (is_null($user->email_verified_at)) {
            $this->otpService->generateAndSend($user, 'telegram');
            return response()->json(['message' => 'Account not verified. A new OTP has been sent.'], 403);
        }

        // Send Telegram Greeting
        $this->telegramService->GreetingMessage($user);

        // ISSUE THE TOKEN HERE
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'access_token' => $token, // The frontend NEEDS this!
            'user' => $user,
            'token_type' => 'Bearer'
        ], 200);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Find the latest unused OTP for this user
        $latestOtp = $user->otps()
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        if (!$latestOtp || !Hash::check($request->otp, $latestOtp->code)) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 401);
        }

        // BUG FIX: Correct syntax for updating the OTP status
        $user->otps()->where('id', $latestOtp->id)->update(['is_used' => true]);


        // Set Role to 'user' if not already set
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $user->roles()->syncWithoutDetaching([$userRole->id]);

            // Tip: Since it's a brand new user, you can also just use attach():
            // $user->roles()->attach($userRole->id);
        }

        // NEW: Mark account as verified if it is their first time verifying
        if (is_null($user->email_verified_at)) {
            $user->update([
                'email_verified_at' => Carbon::now(),
                'status' => 'active',
            ]);
        }

        // Issue token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Verification successful.',
            'access_token' => $token,
            'user' => $user,
            'token_type' => 'Bearer'
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.'], 200);
    }
}