<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\Area\Area;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\RoomIdentifier;
use AqwSocketClient\Objects\Names\AreaName;
use InvalidArgumentException;
use Override;

/**
 * Represents an event triggered after the client successfully joined a specific
 * map in the game world.
 */
final class AreaJoinedEvent implements EventInterface
{
    public function __construct(
        public readonly Area $area,
    ) {}

    /**
     * @return ?AreaJoinedEvent
     *
     * @throws InvalidArgumentException When fail in creates {@see AqwSocketClient\Objects\Area} attributes.
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if ($message instanceof JsonMessage && $message->type === JsonMessageType::JoinedArea) {
            /** @var array{strMapName: string, areaName: string, areaId: numeric-string} $data */
            $data = $message->data;

            $name = new AreaName($data['strMapName']);
            $identifier = new AreaIdentifier((int) $data['areaId']);
            $roomParts = explode('-', $data['areaName']);
            $room = array_key_exists(1, $roomParts) ? new RoomIdentifier((int) $roomParts[1]) : new RoomIdentifier(0);

            $area = new Area($identifier, $name, $room);

            return new self($area);
        }

        return null;
    }
}
