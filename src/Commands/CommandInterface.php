<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Packet;

/**
 * Interface defining a command that can be sent to the AQW server.
 *
 * Implementations of this interface should provide a method to convert the
 * command into a {@see AqwSocketClient\Packet} object suitable for transmission over TCP.
 */
interface CommandInterface
{
    /**
     * Converts the command into a packet to be sent to the server.
     *
     * @return Packet The packet representing the command.
     */
    public function toPacket(): Packet;
}
