<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use Override;

/**
 * Represents an event triggered after the client try to join a area that he is already in.
 */
final class AlreadyInAreaEvent implements EventInterface
{
    /**
     * @return ?AlreadyInAreaEvent
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if ($message instanceof DelimitedMessage && $message->type === DelimitedMessageType::Warning) {
            if ($message->data[0] === 'Cannot join a room you are already in.') {
                return new self();
            }
        }

        return null;
    }
}
