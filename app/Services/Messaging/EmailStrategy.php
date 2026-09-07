<?php

namespace App\Services\Messaging;

use App\Contracts\MessagingStrategy;
use App\Mail\Auth\VerifyEmailOtp;
use Illuminate\Support\Facades\Mail;

class EmailStrategy implements MessagingStrategy
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function sendOTP(\App\Models\User|null $user = null, string $plainOtp): void
    {
        Mail::to($user->email)->send(new VerifyEmailOtp($user, $plainOtp));
    }
}
