<?php

namespace App\Services\Messaging;

use App\Contracts\MessagingStrategy;
use InvalidArgumentException;

class MessagingFactory
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function execute(string $channel): MessagingStrategy
    {
        return match($channel) {
            'email' => app(EmailStrategy::class),
            'telegram' => app(TelegramStrategy::class),
            default => throw new InvalidArgumentException("Unsupported messaging channel: {$channel}"),
        };
    }
}
