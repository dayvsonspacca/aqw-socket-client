<?php

declare(strict_types=1);

namespace AqwSocketClient\Factories;

use AqwSocketClient\Commands\CommandsHandlerInterface;
use AqwSocketClient\Events\ConnectionEstabilishedEvent;

class CoreCommandsHandler implements CommandsHandlerInterface
{
    /**
     * @param EventInterface[] $events
     */
    public function handle(array $events)
    {
        foreach ($events as $event) {
            $eventName = get_class($event);
            
            match ($eventName) {
                ConnectionEstabilishedEvent::class => dump($event),
                default => null
            };
        }
    }
}
