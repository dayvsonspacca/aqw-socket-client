<?php 

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Packet;

class LoadShopCommand implements CommandInterface
{
    public function __construct(
        public readonly int $areaId,
        public readonly int $shopId
    ) {
    }

    public function pack(): Packet
    {
        return Packet::packetify(
            "%xt%zm%loadShop%{$this->areaId}%{$this->shopId}%"
        );
    }
}