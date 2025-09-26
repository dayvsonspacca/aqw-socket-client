<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

/**
 * Marker interface representing a generic AQW event.
 *
 * All events emitted by the client should implement this interface.
 * Event objects represent occurrences or messages received from the server
 * that can be processed by event handlers.
 */
interface EventInterface
{
}
