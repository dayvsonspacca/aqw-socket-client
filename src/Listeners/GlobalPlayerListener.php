<?php

declare(strict_types=1);

namespace AqwSocketClient\Listeners;

use AqwSocketClient\Events\JoinedAreaEvent;
use AqwSocketClient\Events\LoginResponseEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\ListenerInterface;

class GlobalPlayerListener implements ListenerInterface
{
    public int $socketId;
    public int $areaId;

    public function listen(EventInterface $event)
    {
        if ($event instanceof LoginResponseEvent) {
            $this->socketId = $event->socketId;
        }

        if ($event instanceof JoinedAreaEvent) {
            $this->areaId = $event->areaId;
        }
    }
}