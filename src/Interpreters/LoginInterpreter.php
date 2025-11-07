<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Events\ConnectionEstabilishedEvent;
use AqwSocketClient\Events\LoginResponseEvent;
use AqwSocketClient\Interfaces\{MessageInterface, InterpreterInterface};
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\XmlMessage;

class LoginInterpreter implements InterpreterInterface
{
    public function interpret(MessageInterface $message): array
    {
        $events = [];

        if ($message instanceof XmlMessage) {
            if ($message->dom->firstChild?->nodeName === 'cross-domain-policy') {
                $events[] = new ConnectionEstabilishedEvent();
            }
        } else if ($message instanceof DelimitedMessage) {
            if ($message->type === DelimitedMessageType::LoginResponse) {
                $events[] = new LoginResponseEvent((bool) $message->data[0] ?? false);
            }
        }

        return $events;
    }
}
