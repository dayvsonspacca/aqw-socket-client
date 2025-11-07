<?php

declare(strict_types=1);

namespace AqwSocketClient\Messages;

use AqwSocketClient\Enums\JsonCommandType;
use AqwSocketClient\Interfaces\MessageInterface;

class JsonMessage implements MessageInterface
{
    private function __construct(
        public readonly array $commands
    ) {}

    public static function fromString(string $message): JsonMessage|false
    {
        $message = preg_replace('/}\s*{/', '},{', $message);
        $message = '[' . $message . ']';

        $json = json_decode($message, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        $json = array_map(fn($part) => $part['b']['o'], $json);
        $json = array_map(function ($part) {
            $type = JsonCommandType::fromString($part['cmd']);
            if (!$type) {
                return false;
            }

            return new JsonCommand($type, $part);
        }, $json);

        $json = array_filter($json, fn($command) => $command);
        
        if (empty($json)) {
            return false;
        }

        return new self($json);
    }
}
