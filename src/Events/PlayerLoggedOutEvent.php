<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\XmlMessage;

/**
 * Represents an event triggered after the server confirmed that the player's
 * session was successfully terminated.
 */
final class PlayerLoggedOutEvent implements EventInterface
{
    /**
     * @param XmlMessage $message
     */
    public static function from(MessageInterface $message): ?EventInterface
    {
        $action = $message->dom->getElementsByTagName('body')->item(0)?->getAttribute('action');
        if ($action === 'logout') {
            return new self();
        }

        return null;
    }
}
