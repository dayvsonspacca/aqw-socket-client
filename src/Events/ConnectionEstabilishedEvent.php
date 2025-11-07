<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Interfaces\EventInterface;

/**
 * Represents an event indicating that a connection to the AQW server has been established.
 *
 * This event is triggered when the client successfully connects to the server
 * and is ready to send {@see AqwSocketClient\Packet} or receive further {@see AqwSocketClient\Interfaces\MessageInterface}.
 */
class ConnectionEstabilishedEvent implements EventInterface
{
}
