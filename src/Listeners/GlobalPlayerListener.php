<?php

declare(strict_types=1);

namespace AqwSocketClient\Listeners;

use AqwSocketClient\Events\{JoinedAreaEvent, LoginResponseEvent};
use AqwSocketClient\Interfaces\{EventInterface, ListenerInterface};

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
