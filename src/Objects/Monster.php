<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use AqwSocketClient\Objects\Identifiers\MonsterIdentifier;
use AqwSocketClient\Objects\Levels\MonsterLevel;
use AqwSocketClient\Objects\Names\MonsterName;

final readonly class Monster
{
    public function __construct(
        public readonly MonsterIdentifier $identifier,
        public readonly MonsterName $name,
        public readonly MonsterLevel $level,
        public readonly Health $health,
        public readonly GameFileMetadata $metadata,
    ) {}
}
