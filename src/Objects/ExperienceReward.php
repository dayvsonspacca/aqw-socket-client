<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use Psl;

final readonly class ExperienceReward implements QuestRewardInterface
{
    public function __construct(
        public readonly int $amount,
    ) {
        Psl\invariant($this->amount >= 0, 'Experience amount must be non-negative, got %d.', $this->amount);
    }
}
