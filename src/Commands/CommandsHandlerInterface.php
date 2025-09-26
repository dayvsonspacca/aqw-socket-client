<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Events\EventInterface;

interface CommandsHandlerInterface
{
    /**
     * @param EventInterface[] $events
     */
    public function handle(array $events);
}