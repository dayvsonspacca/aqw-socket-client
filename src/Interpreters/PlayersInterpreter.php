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
        return match ($message::class) {
            DelimitedMessage::class => $this->interpretDelimited($message),
            default => []
        };
    }

    private function interpretDelimited(DelimitedMessage $message)
    {
        $events = [];
        if ($message->type === DelimitedMessageType::ExitArea) {
            $events[] = new PlayerDetectedEvent($message->data[1]);
        }
        if ($message->type === DelimitedMessageType::PlayerChange) {
            $events[] = new PlayerDetectedEvent($message->data[0]);
        }

        return $events;
    }
}
