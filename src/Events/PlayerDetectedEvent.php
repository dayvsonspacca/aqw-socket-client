<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\DelimitedMessage;

/**
 * Represents an event triggered when the client receives data indicating
 * that a **player has entered the current screen or area**.
 */
final class PlayerDetectedEvent implements EventInterface
{
    /**
     * @param string $name The **username** of the player that was detected.
     */
    public function __construct(
        public readonly string $name,
    ) {}

    /**
     * @param DelimitedMessage $message
     * @return ?PlayerDetectedEvent
     */
    public static function from(MessageInterface $message): ?EventInterface
    {
        if ($message instanceof DelimitedMessage) {
            if ($message->type === DelimitedMessageType::ExitArea) {
                // @mago-expect analyzer:possibly-undefined-array-index
                return new self($message->data[1]);
            }
            if ($message->type === DelimitedMessageType::PlayerChange) {
                // @mago-expect analyzer:possibly-undefined-array-index
                return new self($message->data[0]);
            }
        }

        return null;
    }
}
