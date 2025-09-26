<?php

declare(strict_types=1);

namespace AqwSocketClient\Factories;

use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Events\ConnectionEstabilishedEvent;
use AqwSocketClient\Events\EventsHandlerInterface;
use AqwSocketClient\Events\RawMessageEvent;

class CoreEventsHandler implements EventsHandlerInterface
{
    public function __construct(
        private readonly string $playerName,
        private readonly string $token
    ) {}

    /**
     * @param EventInterface[] $events
     */
    public function handle(array $events)
    {
        $commands = [];
        foreach ($events as $event) {
            $eventName = get_class($event);

            $commands = array_merge($commands, match ($eventName) {
                ConnectionEstabilishedEvent::class => [
                    new LoginCommand($this->playerName, $this->token)
                ],
                RawMessageEvent::class => [],
                default => []
            });
        }
        return $commands;
    }
}
