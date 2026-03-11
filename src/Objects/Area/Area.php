<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Area;

use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\RoomIdentifier;
use AqwSocketClient\Objects\Names\AreaName;

final readonly class Area
{
    public function __construct(
        public readonly AreaIdentifier $identifier,
        public readonly AreaName $name,
        public readonly ?RoomIdentifier $room,
    ) {}
}
