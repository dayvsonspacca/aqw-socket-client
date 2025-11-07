<?php

declare(strict_types=1);

namespace AqwSocketClient\Messages;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Interfaces\MessageInterface;

class DelimitedMessage implements MessageInterface
{
    private function __construct(
        public readonly DelimitedMessageType $type,
        public readonly array $data
    ) {}

    public static function fromString(string $message): DelimitedMessage|false
    {
        if (!str_starts_with($message, '%') && !str_ends_with($message, '%')) {
            return false;
        }

        $parts = explode('%', $message); 
        $parts = array_filter($parts, fn($part) => !empty($part));

        unset($parts[1]);
        unset($parts[3]);

        $parts = array_values($parts);

        $type = DelimitedMessageType::fromString($parts[0]);
        if (!$type) {
            return false;
        }

        $data = array_filter($parts, fn($key) => $key !== 0, ARRAY_FILTER_USE_KEY);
        
        return new self(
            $type,
            array_values($data)
        );
    }
}
