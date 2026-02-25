<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Messages\JsonMessage;

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
     * @param array<int, array{socket_id: int, name: string}> $players A list of usernames of the players detected in the area at the time of joining.
     * @param array<int, array{name: string, asset_name: string|null, level: int, race: string, hp: int}> $monsters A list of monsters present in the area at the time of joining (if available).
     */
    public function __construct(
        public readonly string $mapName,
        public readonly int $mapNumber,
        public readonly int $areaId,
        public readonly array $players,
        public readonly array $monsters,
    ) {}

    public static function fromJsonMessage(JsonMessage $message): ?self
    {
        if ($message->type !== JsonMessageType::JoinedArea) {
            return null;
        }

        /**
         * @var array{
         *     strMapName: string,
         *     areaId: numeric-string,
         *     areaName: string,
         *     uoBranch: array<int, array{
         *         entID: numeric-string,
         *         strUsername: string
         *     }>,
         *     monBranch?: array<int, array{
         *         MonID: numeric-string,
         *         intHPMax: numeric-string
         *     }>,
         *     mondef?: array<int, array{
         *         MonID: numeric-string,
         *         strMonName: string,
         *         strMonFileName: string,
         *         intLevel: numeric-string,
         *         sRace: string
         *     }>
         * } $data
         */
        $data = $message->data;

        $players = [];
        foreach ($data['uoBranch'] as $player) {
            $players[] = [
                'socket_id' => (int) $player['entID'],
                'name' => $player['strUsername'],
            ];
        }

        $monsters = [];
        foreach ($data['monBranch'] ?? [] as $monster) {
            $monsters[(int) $monster['MonID']] = [
                'hp' => (int) $monster['intHPMax'],
            ];
        }

        foreach ($data['mondef'] ?? [] as $monster) {
            $monsterId = (int) $monster['MonID'];
            $monsters[$monsterId] = array_merge($monsters[$monsterId], [
                'name' => $monster['strMonName'],
                'asset_name' => $monster['strMonFileName'],
                'level' => (int) $monster['intLevel'],
                'race' => $monster['sRace'],
            ]);
        }

        /**
         * @var array<int, array{name: string, asset_name: string|null, level: int, race: string, hp: int}> $monsters
         */

        return new self(
            $data['strMapName'],
            (int) explode('-', $data['areaName'])[1],
            (int) $data['areaId'],
            $players,
            $monsters,
        );
    }
}
