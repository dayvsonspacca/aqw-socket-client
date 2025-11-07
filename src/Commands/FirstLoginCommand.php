<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Packet;

class FirstLoginCommand implements CommandInterface
{
    public function pack(): Packet
    {
        return Packet::packetify('%xt%zm%firstJoin%1%');
    }
}
