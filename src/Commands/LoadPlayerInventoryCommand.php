<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Packet;

class LoadPlayerInventoryCommand implements CommandInterface
{
    public function __construct(
        public readonly int $areaId,
        public readonly int $socketId
    ) {
    }

    public function pack(): Packet
    {
        return Packet::packetify(
            "%xt%zm%retrieveInventory%{$this->areaId}%{$this->socketId}%"
        );
    }
}
