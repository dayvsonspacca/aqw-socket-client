<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

/**
 * Marks a class as an **event** received from the AQW server.
 */
interface EventInterface
{
    public static function from(MessageInterface $message): ?EventInterface;
}
