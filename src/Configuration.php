<?php

declare(strict_types=1);

namespace AqwSocketClient;

class Configuration
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
        public readonly bool $logMessages = false
    ) {}
}
