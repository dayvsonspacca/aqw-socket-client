<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use Override;

/**
 * Represents an event triggered after the client try to join a area
 */
final class AreaNotAvaliableEvent implements EventInterface
{
    /**
     * @return ?AreaNotAvaliableEvent
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if ($message instanceof DelimitedMessage && $message->type === DelimitedMessageType::Warning) {
            if (mb_strpos($message->data[0], 'is not available.') !== false) {
                return new self();
            }
        }

        return null;
    }
}
