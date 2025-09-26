<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Packet;

/**
 * Represents a command sent to the AQW server immediately after a successful login.
 *
 * This command is typically used to perform initial setup actions such as
 * joining the game world or synchronizing client state.
 */
class AfterLoginCommand implements CommandInterface
{
    /**
     * Converts the command into a packet that can be sent to the server.
     *
     * @return Packet The packet containing the command data.
     */
    public function toPacket(): Packet
    {
        $packet = "%xt%zm%firstJoin%1%";

        return Packet::packetify($packet);
    }
}
