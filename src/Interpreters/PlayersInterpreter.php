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
 * Interprets messages related to the players in the area.
 *
 * ### Events:
 * - {@see AqwSocketClient\Events\PlayerDetectedEvent}
 */
final class PlayersInterpreter implements InterpreterInterface
{
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
