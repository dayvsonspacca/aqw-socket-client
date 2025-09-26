<?php

declare(strict_types=1);

namespace AqwSocketClient\Events\Factories;

use AqwSocketClient\Events\{ConnectionEstabilishedEvent, LoginSuccessfulEvent, RawMessageEvent};

/**
 * Factory responsible for creating core events from raw AQW server messages.
 *
 * This factory interprets raw messages and converts them into appropriate event
 * objects such as connection established, login successful, and generic raw messages.
 */
class CoreEventsFactory implements EventsFactoryInterface
{
    /**
     * Creates events from a raw server message.
     *
     * This method always generates a {@see AqwSocketClient\Events\RawMessageEvent}. Additionally, it detects
     * specific patterns in the message to create higher-level events like
     * {@see AqwSocketClient\Events\ConnectionEstabilishedEvent} and {@see AqwSocketClient\Events\LoginSuccessfulEvent}.
     *
     * @param string $message The raw message received from the server.
     * @return array An array of event objects generated from the message.
     */
    public function fromMessage(string $message): array
    {
        $events = [
            new RawMessageEvent($message)
        ];

        if (str_contains($message, '<cross-domain-policy>')) {
            $events[] = new ConnectionEstabilishedEvent();
        }

        if (str_contains($message, '%xt%loginResponse%-1%true%')) {
            $events[] = new LoginSuccessfulEvent();
        }

        return $events;
    }
}
