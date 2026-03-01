<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Objects\Names\PlayerName;
use Override;

/**
 * Represents an event triggered when the client receives data indicating
 * that a **player has entered the current screen or area**.
 */
final class PlayerDetectedEvent implements EventInterface
{
    public function __construct(
        public readonly PlayerName $name,
    ) {}

    /**
     * @return ?PlayerDetectedEvent
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if ($message instanceof DelimitedMessage) {
            if ($message->type === DelimitedMessageType::ExitArea) {
                return new self(new PlayerName($message->data[1]));
            }
            if ($message->type === DelimitedMessageType::PlayerChange) {
                return new self(new PlayerName($message->data[0]));
            }
        }

        return null;
    }
}
