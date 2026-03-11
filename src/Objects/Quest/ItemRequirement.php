<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Quest;

use AqwSocketClient\Objects\Identifiers\ItemIdentifier;

final readonly class ItemRequirement implements QuestRequirementInterface
{
    public function __construct(
        public readonly ItemIdentifier $itemIdentifier,
    ) {}
}
