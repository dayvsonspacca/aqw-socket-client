<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\XmlMessage;

/**
 * Represents an event indicating that a connection to the AQW server has been established.
 *
 * This event is triggered when the client successfully connects to the server
 * and is ready to send {@see AqwSocketClient\Packet} or receive further {@see AqwSocketClient\Interfaces\MessageInterface}.
 */
final class ConnectionEstablishedEvent implements EventInterface
{
    /**
     * @param XmlMessage $message
     * @return ?ConnectionEstablishedEvent
     */
    public static function from(MessageInterface $message): ?EventInterface
    {
        if ($message instanceof XmlMessage) {
            if ($message->dom->firstChild?->nodeName === 'cross-domain-policy') {
                return new self();
            }
        }

        return null;
    }
}
