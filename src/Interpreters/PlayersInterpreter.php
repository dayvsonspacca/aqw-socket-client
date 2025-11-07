<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Events\ConnectionEstabilishedEvent;
use AqwSocketClient\Events\LoginResponseEvent;
use AqwSocketClient\Events\PlayerDetectedEvent;
use AqwSocketClient\Interfaces\{MessageInterface, InterpreterInterface};
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\XmlMessage;

class PlayersInterpreter implements InterpreterInterface
{
    public function interpret(MessageInterface $message): array
    {
        $events = [];

        if ($message instanceof DelimitedMessage) {
            if ($message->type === DelimitedMessageType::ExitArea && count($message->data) === 3) {
                $events[] = new PlayerDetectedEvent($message->data[1]);
            }
            if ($message->type === DelimitedMessageType::PlayerChange) {
                $events[] = new PlayerDetectedEvent($message->data[0]);
            }
        }

        return $events;
    }
}
