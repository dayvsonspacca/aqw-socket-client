<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Packet;

/**
 * Represents a command to instruct the client to join a specific map or area
 * within the game world.
 *
 * This command is sent to the server to initiate a map change or movement.
 *
 * @see AqwSocketClient\Interfaces\CommandInterface
 */
class JoinMapCommand implements CommandInterface
{
    /**
     * @param string $username The username of the player attempting to join the map.
     * @param string $mapName The base name of the map to join (e.g., 'battleon', 'yulgar').
     * @param int $room The specific instance number of the map to join (e.g., 0 for public, 1, 2...). Defaults to 0.
     */
    public function __construct(
        public readonly string $username,
        public readonly string $mapName,
        public readonly int $room = 0
    ) {
    }

    /**
     * Converts the command object into a ready-to-send {@see AqwSocketClient\Packet} object,
     * serializing the join map request according to the AQW protocol.
     *
     * The resulting packet format changes based on whether a specific room number is provided.
     *
     * @return Packet The final packet object ready for transmission.
     */
    public function pack(): Packet
    {
        return Packet::packetify(
            $this->room > 0
                ? "%xt%zm%cmd%1%tfer%{$this->username}%{$this->mapName}-{$this->room}%"
                : "%xt%zm%cmd%1%tfer%{$this->username}%{$this->mapName}%"
        );
    }
}
