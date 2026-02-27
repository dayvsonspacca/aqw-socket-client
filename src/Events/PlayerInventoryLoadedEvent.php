<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\JsonMessage;
use Override;

final class PlayerInventoryLoadedEvent implements EventInterface
{
    /**
     * @return ?PlayerInventoryLoadedEvent
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if ($message instanceof JsonMessage && $message->type === JsonMessageType::InventoryLoaded) {
            return new self();
        }

        return null;
    }
}
