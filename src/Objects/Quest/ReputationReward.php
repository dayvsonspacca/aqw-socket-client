<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Quest;

use AqwSocketClient\Objects\Faction;
use Psl;

final readonly class ReputationReward implements QuestRewardInterface
{
    public function __construct(
        public readonly int $amount,
        public readonly Faction $faction,
    ) {
        Psl\invariant($this->amount >= 0, 'Reputation amount must be non-negative, got %d.', $this->amount);
    }
}
