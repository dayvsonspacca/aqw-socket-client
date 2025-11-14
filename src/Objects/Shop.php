<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

class Shop implements IteratorAggregate, Countable
{
    public const ITEMS = 0;
    public const HOUSE = 1;

    /**
     * @param Item[] $items
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $type,
        public readonly bool $memberOnly,
        public readonly array $items
    ) {
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }
}
