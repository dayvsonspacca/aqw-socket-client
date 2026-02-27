<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\AreaIdentifier;
use InvalidArgumentException;

/**
 * Represents an event triggered after the client successfully joined a specific
 * map in the game world.
 */
final class AreaJoinedEvent implements EventInterface
{
    /**
     * @param string $mapName The name of the map or area that was joined (e.g., 'battleon').
     * @param int $mapNumber The specific map instance number that was assigned (e.g., 1, 2, 3...).
     * @param AreaIdentifier $areaId The server ID of the map.
     */
    public function __construct(
        public readonly string $mapName,
        public readonly int $mapNumber,
        public readonly AreaIdentifier $areaId,
    ) {}

    /**
     * @param JsonMessage $message
     * @return ?AreaJoinedEvent
     *
     * @throws InvalidArgumentException WHen area id in data is negative or zero.
     */
    public static function from(MessageInterface $message): ?EventInterface
    {
        if ($message instanceof JsonMessage && $message->type === JsonMessageType::JoinedArea) {
            /** @var array{strMapName: string, areaName: string, areaId: numeric-string} $data */
            $data = $message->data;

            return new self(
                $data['strMapName'],
                (int) explode('-', $data['areaName'])[1],
                new AreaIdentifier((int) $data['areaId']),
            );
        }

        return null;
    }
}
