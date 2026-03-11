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
use Override;
use Psl\Str;

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
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if ($message instanceof JsonMessage && $message->type === JsonMessageType::JoinedArea) {
            /** @var array{strMapName: string, areaName: string, areaId: numeric-string} $data */
            $data = $message->data;

            $name = new AreaName($data['strMapName']);
            $identifier = new AreaIdentifier((int) $data['areaId']);
            $roomParts = Str\split($data['areaName'], '-');
            $roomPart = $roomParts[1] ?? null;
            $room = $roomPart !== null ? new RoomIdentifier((int) $roomPart) : null;

            $area = new Area($identifier, $name, $room);

            return new self($area);
        }

        return null;
    }
}
