<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use AqwSocketClient\Objects\Identifiers\ItemIdentifier;
use Psl;
use Psl\Type;

final readonly class QuestReward
{
    public function __construct(
        public readonly ItemIdentifier $itemIdentifier,
        public readonly float $rate,
        public readonly int $quantity,
    ) {
        Psl\invariant($this->rate > 0.0 && $this->rate <= 100.0, 'Rate must be between 0 and 100 (exclusive), got %f.', $this->rate);
        Type\positive_int()->assert($this->quantity);
    }
}
