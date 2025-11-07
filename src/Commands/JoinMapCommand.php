<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Packet;

class JoinMapCommand implements CommandInterface
{
    public function __construct(
        public readonly string $username,
        public readonly string $mapName,
        public readonly int $room = 0
    ) {
    }

    public function pack(): Packet
    {
        return Packet::packetify(
            $this->room > 0
                ? "%xt%zm%cmd%1%tfer%{$this->username}%{$this->mapName}-{$this->room}%"
                : "%xt%zm%cmd%1%tfer%{$this->username}%{$this->mapName}%"
        );
    }
}
