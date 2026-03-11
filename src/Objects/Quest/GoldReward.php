<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Quest;

use Psl;

final readonly class GoldReward implements QuestRewardInterface
{
    public function __construct(
        public readonly int $amount,
    ) {
        Psl\invariant($this->amount >= 0, 'Gold amount must be non-negative, got %d.', $this->amount);
    }
}
