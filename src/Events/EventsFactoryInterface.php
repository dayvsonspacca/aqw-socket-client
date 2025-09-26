<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Events\EventInterface;

/**
 * Interface defining a factory responsible for converting raw messages into {@see AqwSocketClient\Events\EventInterface} objects.
 */
interface EventsFactoryInterface
{
    /**
     * Creates {@see AqwSocketClient\Events\EventInterface} objects from a raw socket message.
     *
     * @param string $message The raw message received from the server.
     * @return EventInterface[]
     */
    public function fromMessage(string $message): array;
}
