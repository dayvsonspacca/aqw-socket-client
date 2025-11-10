<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Packet;

class LoadPlayerCommand implements CommandInterface
{
    public function __construct(
        public readonly int $areaId,
        public readonly int $socketId
    ) {
    }

    public function pack(): Packet
    {
        return Packet::packetify(
            "%xt%zm%retrieveUserDatas%{$this->areaId}%{$this->socketId}%"
        );
    }
}
