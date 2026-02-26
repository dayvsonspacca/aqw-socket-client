<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Interfaces\InterpreterInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\JsonMessage;
use InvalidArgumentException;
use Override;

/**
 * Interprets messages related to the player's location and area transitions.
 *
 * ### Events:
 * - {@see AqwSocketClient\Events\AreaJoinedEvent}
 */
final class AreaInterpreter implements InterpreterInterface
{
    /**
     * @throws InvalidArgumentException When interprete a {@see AqwSocketClient\Events\AreaJoinedEvent} and area id is negative or zero.
     */
    #[Override]
    public function interpret(MessageInterface $message): array
    {
        return match ($message::class) {
            JsonMessage::class => array_filter([
                AreaJoinedEvent::fromJsonMessage($message),
            ]),
            default => [],
        };
    }
}
