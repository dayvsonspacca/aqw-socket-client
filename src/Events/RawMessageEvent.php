<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

/**
 * Represents a generic event containing a raw message received from the server.
 *
 * This event is triggered for every raw message and can be used for logging or
 * debugging purposes. The message is automatically echoed to the console when the
 * event is instantiated.
 */
class RawMessageEvent implements EventInterface
{
    /**
     * RawMessageEvent constructor.
     *
     * @param string $message The raw message received from the server.
     */
    public function __construct(public readonly string $message)
    {
    }
}
