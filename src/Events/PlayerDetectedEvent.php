<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Interfaces\EventInterface;

/**
 * Represents an event triggered when the client receives data indicating
 * that a **player has entered the current screen or area**.
 *
 */
class PlayerDetectedEvent implements EventInterface
{
    /**
     * @param string $name The **username** of the player that was detected.
     */
    public function __construct(
        public readonly string $name
    ) {
    }
}
