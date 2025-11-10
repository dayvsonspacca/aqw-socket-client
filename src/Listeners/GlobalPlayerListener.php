<?php

declare(strict_types=1);

namespace AqwSocketClient\Listeners;

use AqwSocketClient\Events\LoginResponseEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\ListenerInterface;

class GlobalPlayerListener implements ListenerInterface
{
    public int $socketId;

    public function listen(EventInterface $event)
    {
        if ($event instanceof LoginResponseEvent) {
            $this->socketId = $event->socketId;
        }
    }
}