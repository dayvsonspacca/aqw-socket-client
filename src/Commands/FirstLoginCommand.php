<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Packet;

/**
 * Represents the initial **'firstJoin' command** sent to the AQW server
 * immediately after a successful socket connection.
 *
 * This command is typically required by the server protocol to initiate
 * the client's session and receive the initial data.
 *
 * Move player to battleon
 */
class FirstLoginCommand implements CommandInterface
{
    /**
     * Serializes the command into the raw string format expected by the server
     * and wraps it in a {@see AqwSocketClient\Packet} object.
     *
     * @return Packet The final packet object containing the '%xt%zm%firstJoin%1%' string.
     */
    public function pack(): Packet
    {
        return Packet::packetify('%xt%zm%firstJoin%1%');
    }
}
