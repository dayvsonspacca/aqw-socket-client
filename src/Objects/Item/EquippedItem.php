<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Item;

use AqwSocketClient\Enums\EquipmentSlot;
use AqwSocketClient\Objects\GameFileMetadata;
use AqwSocketClient\Objects\Identifiers\ItemIdentifier;

final readonly class EquippedItem
{
    public function __construct(
        public readonly ItemIdentifier $identifier,
        public readonly EquipmentSlot $slot,
        public readonly GameFileMetadata $metadata,
        public readonly ?string $boost,
    ) {}
}
