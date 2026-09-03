<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class TelegramService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function sendOtpToGroup($message)
    {

        
        Http::post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
            'chat_id' => env('TELEGRAM_GROUP_ID'),
            'text' => $message,
            'parse_mode' => 'Markdown'
        ]);
    }
    public function GreetingMessage($user)
    {
        Http::post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
            'chat_id' => env('TELEGRAM_GROUP_ID'),
            'text' => "*Welcome, {$user->name}\\!*\n\nYou have successfully logged in\\.",
            'parse_mode' => 'MarkdownV2'
        ]);

    }
}
