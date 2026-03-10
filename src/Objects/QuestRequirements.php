<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use Psl;
use Psl\Type;

final readonly class QuestRequirements
{
    /**
     * @param list<Item> $items
     */
    public function __construct(
        public readonly int $level,
        public readonly int $reputation,
        public readonly int $classPoints,
        public readonly array $items,
    ) {
        Psl\invariant($this->level >= 0, 'Required level must be non-negative, got %d.', $this->level);
        Psl\invariant($this->reputation >= 0, 'Required reputation must be non-negative, got %d.', $this->reputation);
        Psl\invariant($this->classPoints >= 0, 'Required class points must be non-negative, got %d.', $this->classPoints);
        Type\vec(Type\instance_of(Item::class))->assert($this->items);
    }
}
