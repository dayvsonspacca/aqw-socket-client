<?php

declare(strict_types=1);

namespace AqwSocketClient;

/**
 * Poor representation of an AQW server
 */
class Server
{
    private function __construct(
        public readonly string $hostname,
        public readonly int $port
    ) {}

    public static function espada(): Server
    {
        return new self('socket2.aq.com', 5591);
    }
}
