<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Quest;

use AqwSocketClient\Objects\Faction;
use Psl;

final readonly class ReputationRequirement implements QuestRequirementInterface
{
    public function __construct(
        public readonly int $reputation,
        public readonly Faction $faction,
    ) {
        Psl\invariant($this->reputation >= 0, 'Required reputation must be non-negative, got %d.', $this->reputation);
    }
}
