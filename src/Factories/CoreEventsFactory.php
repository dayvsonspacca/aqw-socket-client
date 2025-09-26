<?php

declare(strict_types=1);

namespace AqwSocketClient\Factories;

use AqwSocketClient\Events\ConnectionEstabilishedEvent;
use AqwSocketClient\Events\EventsFactoryInterface;
use AqwSocketClient\Events\LoginSuccessfulEvent;
use AqwSocketClient\Events\RawMessageEvent;

class CoreEventsFactory implements EventsFactoryInterface
{
    public function fromMessage(string $message): array
    {
        $events = [
            new RawMessageEvent($message)
        ];

        if (str_contains($message, "<cross-domain-policy>")) {
            $events[] = new ConnectionEstabilishedEvent();
        }

        if (str_contains($message, "%xt%loginResponse%-1%true%")) {
            $events[] = new LoginSuccessfulEvent();
        }

        return $events;
    }
}
