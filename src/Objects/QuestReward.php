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
        public readonly int $rate,
        public readonly int $quantity,
        public readonly bool $guaranteed,
    ) {
        Psl\invariant($this->rate >= 1 && $this->rate <= 100, 'Rate must be between 1 and 100, got %d.', $this->rate);
        Type\positive_int()->assert($this->quantity);
    }
}
