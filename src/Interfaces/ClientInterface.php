<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

/**
 * Represents the game client to a {@see AqwSocketClient\Server}
 */
interface ClientInterface
{
    public function connect(): void;

    public function disconnect(): void;

    public function isConnected(): bool;

    /**
     * Returns the next messages from server
     *
     * @return MessageInterface[]
     * */
    public function receive(): array;
}
