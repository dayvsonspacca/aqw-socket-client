<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

/**
 * Represents a raw, **uninterpreted message** received directly from the
 * AQW server socket.
 */
interface MessageInterface
{
    /**
     * Attempts to create a MessageInterface object from a raw string.
     *
     * @param string $message The raw string data received from the socket.
     * @return self|false The newly created message object, or **false** on failure or not specified how to create
     */
    public static function fromString(string $message): self|false;
}
