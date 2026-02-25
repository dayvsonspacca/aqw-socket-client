<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Packet;
use Override;

/**
 * Represents a command to gracefully log out the current player from the AQW server.
 *
 * This command is sent to terminate the player's active session.
 */
final class LogoutCommand implements CommandInterface
{
    #[Override]
    public function pack(): Packet
    {
        return Packet::packetify('%xt%zm%cmd%1%logout%');
    }
}
