<?php

namespace App\Service;

use App\Mail\Auth\VerifyEmailOtp;
use Illuminate\Support\Facades\Mail;
class EmailService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function sendVerificationOtp($user, $plainOtp)
    {
        $message = "Your verification code is: *{$plainOtp}*. Expires in 5 minutes.";
        Mail::to($user->email)->send(new VerifyEmailOtp($user, $plainOtp));
    }
}
