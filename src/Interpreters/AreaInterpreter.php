<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Interfaces\InterpreterInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\JsonMessage;
use Override;

/**
 * Interprets messages related to the player's location and area transitions.
 */
final class AreaInterpreter implements InterpreterInterface
{
    /**
     * @param MessageInterface $message The message received from the socket client.
     * @return array An array of domain events generated from the message.
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
