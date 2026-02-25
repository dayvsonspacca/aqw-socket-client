<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Events\PlayerDetectedEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\InterpreterInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use Override;

/**
 * An interpreter responsible for parsing incoming server messages that are
 * related to the presence and movement of **other players** in the current area.
 *
 * It generates events for players entering the screen/area.
 */
final class PlayersInterpreter implements InterpreterInterface
{
    /**
     * @param MessageInterface $message The raw, uninterpreted message object.
     * @return array The list of {@see AqwSocketClient\Interfaces\EventInterface} objects generated from the message.
     */
    #[Override]
    public function interpret(MessageInterface $message): array
    {
        return match ($message::class) {
            DelimitedMessage::class => $this->interpretDelimited($message),
            default => [],
        };
    }

    /** @return EventInterface[] */
    private function interpretDelimited(DelimitedMessage $message): array
    {
        $events = [];
        if ($message->type === DelimitedMessageType::ExitArea) {
            // @mago-expect analyzer:possibly-undefined-array-index
            $events[] = new PlayerDetectedEvent($message->data[1]);
        }
        if ($message->type === DelimitedMessageType::PlayerChange) {
            // @mago-expect analyzer:possibly-undefined-array-index
            $events[] = new PlayerDetectedEvent($message->data[0]);
        }

        return $events;
    }
}
