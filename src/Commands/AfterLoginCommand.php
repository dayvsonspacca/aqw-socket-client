<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Packet;

class AfterLoginCommand implements CommandInterface
{
    public function toPacket(): Packet
    {
        $packet = "%xt%zm%firstJoin%1%";

        return Packet::packetify($packet);
    }
}
