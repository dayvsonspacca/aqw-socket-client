<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Quest;

use AqwSocketClient\Objects\Identifiers\ClassIdentifier;
use AqwSocketClient\Objects\Levels\Rank;

final readonly class ClassRankRequirement implements QuestRequirementInterface
{
    public function __construct(
        public readonly ClassIdentifier $classIdentifier,
        public readonly Rank $rank,
    ) {}
}
