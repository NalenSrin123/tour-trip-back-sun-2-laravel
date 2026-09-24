<?php

namespace App\Services\Messaging;

use App\Contracts\MessagingStrategy;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class TelegramStrategy implements MessagingStrategy
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function sendOTP(?User $user = null, string $plainOtp): void
    {
        $message = "Your verification code is: *{$plainOtp}*. Expires in 5 minutes.\nEmail: {$user->email}\nName: {$user->name}\nPlease use this code to verify your account.";

        Http::post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
            'chat_id' => env('TELEGRAM_GROUP_ID'),
            'text' => $message,
            'parse_mode' => 'Markdown'
        ]);
    }

    public function GreetingMessage(?User $user = null): void
    {
        $message = "*Welcome, {$user->name}\\!*\n\nYou have successfully logged in\\.";
        Http::post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
            'chat_id' => env('TELEGRAM_GROUP_ID'),
            'text' => $message,
            'parse_mode' => 'MarkdownV2'
        ]);
    }
}
