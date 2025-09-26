<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

/**
 * Represents a generic event wrapping a raw message
 */
class RawMessageEvent implements EventInterface
{
    public function __construct(public readonly string $message)
    {
        echo "[RAW] - " . $message . PHP_EOL;
    }
}
