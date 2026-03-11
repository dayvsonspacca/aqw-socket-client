<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Quest;

use AqwSocketClient\Objects\Identifiers\FactionIdentifier;
use AqwSocketClient\Objects\Levels\Rank;

final readonly class ReputationRequirement implements QuestRequirementInterface
{
    public function __construct(
        public readonly FactionIdentifier $factionIdentifier,
        public readonly Rank $rank,
    ) {}
}
