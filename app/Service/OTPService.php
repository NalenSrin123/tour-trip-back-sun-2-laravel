<?php

namespace App\Service;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OTPService
{
    /**
     * Create a new class instance.
     */
    private $telegramService;
    private $emailService;
    public function __construct(TelegramService $telegramService, EmailService $emailService)
    {
        $this->telegramService = $telegramService;
        $this->emailService = $emailService;
    }
    public function generateAndSend(User $user, $requestedChannel = 'telegram')
    {
        $plainOtp = random_int(100000, 999999);

        // Save OTP to database
        $user->otps()->create([
            'code' => Hash::make($plainOtp),
            'expires_at' => Carbon::now()->addMinutes(5),
            'is_used' => false
        ]);

        $message = "Your verification code is: *{$plainOtp}*. Expires in 5 minutes.\nEmail: {$user->email}\nName: {$user->name}\nPlease use this code to verify your account.";

        // If user explicitly requested email, use email
        if ($requestedChannel === 'email') {
            $this->emailService->sendVerificationOtp($user, $plainOtp);
            return 'email';
        }

        // Default behavior: Always prioritize Telegram
        $this->telegramService->sendOtpToGroup($message);
        return 'telegram';
    }
}
