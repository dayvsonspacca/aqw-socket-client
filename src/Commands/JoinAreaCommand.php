<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Objects\Identifiers\RoomIdentifier;
use AqwSocketClient\Objects\Names\AreaName;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Packet;
use Override;

/**
 * Represents a command to instruct the client to join a specific map or area
 * within the game world.
 *
 * This command is sent to the server to initiate a map change or movement.
 *
 * @see AqwSocketClient\Interfaces\CommandInterface
 */
final class JoinAreaCommand implements CommandInterface
{
    public function __construct(
        public readonly PlayerName $playerName,
        public readonly AreaName $areaName,
        public readonly ?RoomIdentifier $room = null,
    ) {}

    #[Override]
    public function pack(): Packet
    {
        return Packet::packetify(
            $this->room === null
                ? "%xt%zm%cmd%1%tfer%{$this->playerName}%{$this->areaName}%"
                : "%xt%zm%cmd%1%tfer%{$this->playerName}%{$this->areaName}-{$this->room}%",
        );
    }
}
