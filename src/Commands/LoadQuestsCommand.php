<?php

declare(strict_types=1);

namespace AqwSocketClient\Commands;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\QuestIdentifier;
use AqwSocketClient\Packet;

final class LoadQuestsCommand implements CommandInterface
{
    public function __construct(
        public readonly AreaIdentifier $areaIdentifier,
        public readonly QuestIdentifier $questIdentifier
    ) {}

    public function pack(): Packet
    {
        return Packet::packetify("%xt%zm%getQuests%{$this->areaIdentifier}%{$this->questIdentifier}%");
    }
}
