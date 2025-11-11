<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Interfaces\EventInterface;

/**
 * Represents an event triggered when the client successfully joins a specific
 * map or area in the game world.
 *
 * This event contains details about the location and the list of players
 * currently present in that area.
 */
class JoinedAreaEvent implements EventInterface
{
    /**
     * @param string $mapName The name of the map or area the player joined (e.g., 'battleon').
     * @param int $mapNumber The specific map instance number (e.g., 1, 2, 3...).
     * @param int $areaId The ID of the screen or 'area' within the map.
     * @param string[] $players A list of usernames of the players currently detected in this area.
     */
    public function __construct(
        public readonly string $mapName,
        public readonly int $mapNumber,
        public readonly int $areaId,
        public readonly array $players
    ) {
    }
}