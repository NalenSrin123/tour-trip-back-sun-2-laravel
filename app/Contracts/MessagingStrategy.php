<?php

namespace App\Contracts;

use App\Models\User;

interface MessagingStrategy
{
    public function sendOTP(?User $user=null, string $plainOtp): void;
}
