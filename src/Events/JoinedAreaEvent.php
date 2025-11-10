<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Interfaces\EventInterface;

class JoinedAreaEvent implements EventInterface
{
    /** @param string[] $players */
    public function __construct(
        public readonly string $mapName,
        public readonly int $mapNumber,
        public readonly int $areaId,
        public readonly array $players
    ) {
    }
}
