<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Events\ConnectionEstabilishedEvent;
use AqwSocketClient\Interfaces\{MessageInterface, InterpreterInterface};
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
        }

        return $events;
    }
}