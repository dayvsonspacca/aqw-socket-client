<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

use AqwSocketClient\Packet;
use AqwSocketClient\Scripts\ClientContext;

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

    public function send(Packet $packet): void;

    public function run(ScriptInterface $script): void;

    /**
     * Returns the context from the last {@see ClientInterface::run()} call.
     *
     * Returns null if {@see ClientInterface::run()} has not been called yet.
     */
    public function context(): ?ClientContext;
}
