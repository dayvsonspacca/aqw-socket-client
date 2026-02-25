<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Packet;
use Override;

/**
 * Represents the initial **'firstJoin' command** sent to the AQW server
 * immediately after a successful socket connection.
 *
 * This command is typically required by the server protocol to initiate
 * the client's session and receive the initial data.
 *
 * Move player to battleon
 */
final class JoinInitialAreaCommand implements CommandInterface
{
    #[Override]
    public function pack(): Packet
    {
        return Packet::packetify('%xt%zm%firstJoin%1%');
    }
}
