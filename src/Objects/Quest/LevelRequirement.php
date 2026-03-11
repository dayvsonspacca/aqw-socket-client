<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Quest;

use AqwSocketClient\Objects\Levels\PlayerLevel;

final readonly class LevelRequirement implements QuestRequirementInterface
{
    public function __construct(
        public readonly PlayerLevel $level,
    ) {}
}
