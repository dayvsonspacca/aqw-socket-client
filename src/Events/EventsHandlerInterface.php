<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Events\EventInterface;

interface EventsHandlerInterface
{
    /**
     * @param EventInterface[] $events
     * @return CommandInterface[]
     */
    public function handle(array $events);
}