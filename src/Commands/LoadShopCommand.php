<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Objects\AreaIdentifier;
use AqwSocketClient\Packet;
use Override;

/**
 * Represents a command sent from the client to the server to request
 * the data for a specific shop ID within the current area.
 *
 * This command triggers a server response that typically results in a
 * {@see AqwSocketClient\Events\ShopLoadedEvent}.
 */
final class LoadShopCommand implements CommandInterface
{
    /**
     * @param AreaIdentifier $areaId The ID of the current screen/area where the player is located.
     * @param int $shopId The unique identifier of the shop being requested.
     */
    public function __construct(
        public readonly AreaIdentifier $areaId,
        public readonly int $shopId,
    ) {}

    #[Override]
    public function pack(): Packet
    {
        return Packet::packetify("%xt%zm%loadShop%{$this->areaId->value}%{$this->shopId}%");
    }
}
