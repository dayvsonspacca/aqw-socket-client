<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Interfaces\EventInterface;

/**
 * Represents an event triggered after the server confirmed that the player's
 * session was successfully terminated.
 */
final class PlayerLoggedOutEvent implements EventInterface
{
}
