<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password_hash' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password_hash),
            // Note: email_verified_at is null by default
        ]);

        // Send the verification OTP
        $this->sendOtpToGroup($user);

        return response()->json([
            'message' => 'User registered. Please check the Telegram group for your verification OTP.',
            'user' => $user,
        ], 201);
    }

    private function sendOtpToGroup(User $user)
    {
        $plainOtp = rand(100000, 999999);

        // 1. Save to the separate OTPs table
        $user->otps()->create([
            'code' => Hash::make($plainOtp),
            'expires_at' => Carbon::now()->addMinutes(5),
            'is_used' => false
        ]);

        // 2. Format the message
        $message = "OTP request for {$user->email}\nCode: *{$plainOtp}*\nExpires in 5 minutes.";

        // 3. Send to the Group Chat defined in .env
        Http::post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
            'chat_id' => env('TELEGRAM_GROUP_ID'),
            'text' => $message,
            'parse_mode' => 'Markdown'
        ]);
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
            $this->sendOtpToGroup($user);
            return response()->json(['message' => 'Account not verified. A new OTP has been sent.'], 403);
        }

        // Send Telegram Greeting
        $this->botTelegramGreetingMessage($user);

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

        // NEW: Mark account as verified if it is their first time verifying
        if (is_null($user->email_verified_at)) {
            $user->update(['email_verified_at' => Carbon::now()]);
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
    public function botTelegramGreetingMessage($user)
    {
        Http::post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
            'chat_id' => env('TELEGRAM_GROUP_ID'),
            'text' => "*Welcome, {$user->name}\\!*\n\nYou have successfully logged in\\.",
            'parse_mode' => 'MarkdownV2'
        ]);

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.'], 200);
    }
}