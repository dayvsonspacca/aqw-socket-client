<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Packet;

/**
 * Represents a command sent to the server to request the loading and retrieval
 * of the current player's inventory data.
 *
 * @see AqwSocketClient\Interfaces\CommandInterface
 */
class LoadPlayerInventoryCommand implements CommandInterface
{
    /**
     * @param int $areaId The ID of the current screen or area the player is in.
     * @param int $socketId The temporary socket ID for the current client connection.
     */
    public function __construct(
        public readonly int $areaId,
        public readonly int $socketId
    ) {
    }

    /**
     * Converts the command object into a ready-to-send {@see AqwSocketClient\Packet} object,
     * serializing the inventory retrieval request according to the AQW protocol.
     *
     * @return Packet The final packet object ready for transmission.
     */
    public function pack(): Packet
    {
        return Packet::packetify(
            "%xt%zm%retrieveInventory%{$this->areaId}%{$this->socketId}%"
        );
    }
}