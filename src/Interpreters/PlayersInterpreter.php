<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\{DelimitedMessageType, JsonCommandType};
use AqwSocketClient\Events\{PlayerDetectedEvent};
use AqwSocketClient\Interfaces\{InterpreterInterface, MessageInterface};
use AqwSocketClient\Messages\{DelimitedMessage};

/**
 * An interpreter responsible for parsing incoming server messages that are
 * related to the presence and movement of **other players** in the current area.
 *
 * It generates events for players entering the screen/area.
 */
class PlayersInterpreter implements InterpreterInterface
{
    /** @var EventInterface[] $events */
    private array $events = [];

    /**
     * Currently handles:
     * - **ExitArea** delimited messages (for player detection, based on your logic).
     * - **PlayerChange** delimited messages (for player movement/presence updates).
     *
     * Both result in a {@see AqwSocketClient\Events\PlayerDetectedEvent} with the player's name.
     *
     * @param MessageInterface $message The raw, uninterpreted message object.
     * @return array The list of {@see AqwSocketClient\Interfaces\EventInterface} objects generated from the message.
     */
    public function interpret(MessageInterface $message): array
    {
        match ($message::class) {
            DelimitedMessage::class => $this->interpretDelimited($message),
            default => null
        };

        return $this->events;
    }

    private function interpretDelimited(DelimitedMessage $message)
    {
        if ($message->type === DelimitedMessageType::ExitArea) {
            $this->events[] = new PlayerDetectedEvent($message->data[1]);
        }
        if ($message->type === DelimitedMessageType::PlayerChange) {
            $this->events[] = new PlayerDetectedEvent($message->data[0]);
        }
    }
}
