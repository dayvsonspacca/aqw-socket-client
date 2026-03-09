<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use AqwSocketClient\Objects\Identifiers\ItemIdentifier;

final class QuestReward
{
    public function __construct(
        public readonly ItemIdentifier $itemIdentifier,
        public readonly float $rate,
        public readonly int $quantity,
        public readonly bool $guaranteed,
    ) {}
}
