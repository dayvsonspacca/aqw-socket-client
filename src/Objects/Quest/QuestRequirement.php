<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Quest;

use AqwSocketClient\Objects\Identifiers\QuestIdentifier;

final readonly class QuestRequirement implements QuestRequirementInterface
{
    public function __construct(
        public readonly QuestIdentifier $questIdentifier,
    ) {}
}
