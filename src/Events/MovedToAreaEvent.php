<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Interfaces\EventInterface;

/**
 * Representes when the player move to an area.
 */
class MovedToAreaEvent implements EventInterface
{
    public function __construct(
        public readonly int $userId,
        public readonly int $areaId,
        public readonly array $players,
        public readonly string $mapName
    ) {
    }
}
