<?php 

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Packet;

/**
 * Represents a command sent from the client to the server to request
 * the data for a specific shop ID within the current area.
 *
 * This command triggers a server response that typically results in a
 * {@see AqwSocketClient\Events\ShopLoadedEvent}.
 */
class LoadShopCommand implements CommandInterface
{
    /**
     * @param int $areaId The ID of the current screen/area where the player is located.
     * @param int $shopId The unique identifier of the shop being requested.
     */
    public function __construct(
        public readonly int $areaId,
        public readonly int $shopId
    ) {
    }

    /**
     * Converts the command object into a ready-to-send {@see AqwSocketClient\Packet} object.
     * @return Packet The final packet object ready for transmission.
     */
    public function pack(): Packet
    {
        return Packet::packetify(
            "%xt%zm%loadShop%{$this->areaId}%{$this->shopId}%"
        );
    }
}