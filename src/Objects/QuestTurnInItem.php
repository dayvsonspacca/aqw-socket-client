<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use AqwSocketClient\Objects\Identifiers\ItemIdentifier;

final class QuestTurnInItem
{
    public function __construct(
        public readonly ItemIdentifier $itemIdentifier,
        public readonly int $quantity,
    ) {}
}
