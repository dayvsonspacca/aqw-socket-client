<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

interface ClientInterface
{
    public function connect(): void;

    public function disconnect(): void;

    public function isConnected(): bool;
}
