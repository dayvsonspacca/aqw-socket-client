<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Events\EventInterface;

/**
 * Interface defining a factory responsible for converting raw server messages
 * into {@see AqwSocketClient\Events\EventInterface} objects.
 *
 * Implementations of this interface should parse raw messages and create
 * appropriate event objects that can later be handled by event handlers.
 */
interface EventsFactoryInterface
{
    /**
     * Creates event objects from a raw socket message.
     *
     * @param string $message The raw message received from the server.
     * @return EventInterface[] An array of event objects generated from the message.
     */
    public function fromMessage(string $message): array;
}
