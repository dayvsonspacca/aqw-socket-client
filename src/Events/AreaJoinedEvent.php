<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Interfaces\EventInterface;

/**
 * Represents an event triggered after the client successfully joined a specific
 * map or area in the game world.
 *
 * This event contains details about the location and the list of players
 * that were present in that area at the time of joining.
 */
final class AreaJoinedEvent implements EventInterface
{
    /**
     * @param string $mapName The name of the map or area that was joined (e.g., 'battleon').
     * @param int $mapNumber The specific map instance number that was assigned (e.g., 1, 2, 3...).
     * @param int $areaId The ID of the screen or 'area' within the map that was entered.
     * @param string[] $players A list of usernames of the players detected in the area at the time of joining.
     * @param array<int, array{name: string, asset_name: string, level: int, race: string, hp: int}> $monsters A list of monsters present in the area at the time of joining (if available).
     */
    public function __construct(
        public readonly string $mapName,
        public readonly int $mapNumber,
        public readonly int $areaId,
        public readonly array $players,
        public readonly array $monsters,
    ) {}
}
