<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Interfaces\InterpreterInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\JsonMessage;
use Override;

/**
 * Interprets messages related to the player's data, such as inventory.
 *
 * ### Events
 * - {@see AqwSocketClient\Events\PlayerInventoryLoadedEvent}
 */
final class PlayerInterpreter implements InterpreterInterface
{
    #[Override]
    public function interpret(MessageInterface $message): array
    {
        return match ($message::class) {
            JsonMessage::class => array_filter([
                PlayerInventoryLoadedEvent::fromJsonMessage($message),
            ]),
            default => [],
        };
    }
}
