<?php

namespace App\Services;

use App\Models\User;
use App\Services\Messaging\EmailStrategy;
use App\Services\Messaging\MessagingFactory;
use App\Services\Messaging\TelegramStrategy;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OTPService
{
    /**
     * Create a new class instance.
     */
    private $telegramService;
    private $emailService;
    public function __construct(TelegramStrategy $telegramService, EmailStrategy $emailService)
    {
        $this->telegramService = $telegramService;
        $this->emailService = $emailService;
    }
    public function generateAndSend(User $user, $channel = 'telegram')
    {
        $plainOtp = random_int(100000, 999999);

        // Save OTP to database
        $user->otps()->create([
            'code' => Hash::make($plainOtp),
            'expires_at' => Carbon::now()->addMinutes(5),
            'is_used' => false
        ]);

        // 2. Resolve the correct strategy
        $strategy = MessagingFactory::execute($channel);

        // 3. Execute delivery
        $strategy->sendOTP($user, $plainOtp);
    }
}
