<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

/**
 * Represents an event indicating that a connection to the AQW server has been established.
 *
 * This event is triggered when the client successfully connects to the server
 * and is ready to send or receive further messages.
 */
class ConnectionEstabilishedEvent implements EventInterface
{
}
