<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use Override;

/**
 * Represents an event triggered after the client dont do anything for a while
 */
final class AwayFromKeyboardEvent implements EventInterface
{
    /**
     * @return ?AwayFromKeyboardEvent
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if ($message instanceof DelimitedMessage && $message->type === DelimitedMessageType::Server) {
            if ($message->data[0] === 'Your status is now Away From Keyboard (AFK).') {
                return new self();
            }
        }

        return null;
    }
}
