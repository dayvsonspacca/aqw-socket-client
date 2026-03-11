<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use Psl;

final readonly class ClassPointsRequirement implements QuestRequirementInterface
{
    public function __construct(
        public readonly int $classPoints,
    ) {
        Psl\invariant(
            $this->classPoints >= 0,
            'Required class points must be non-negative, got %d.',
            $this->classPoints,
        );
    }
}
