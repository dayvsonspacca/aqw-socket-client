<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Quest;

use AqwSocketClient\Objects\Identifiers\ItemIdentifier;
use Psl\Type;

final readonly class QuestTurnInItem
{
    public function __construct(
        public readonly ItemIdentifier $itemIdentifier,
        public readonly int $quantity,
    ) {
        Type\positive_int()->assert($this->quantity);
    }
}
