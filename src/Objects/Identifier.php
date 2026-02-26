<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use InvalidArgumentException;

abstract class Identifier
{
    /**
     * @throws InvalidArgumentException When value negative or zero.
     */
    public function __construct(
        public readonly int $value,
    ) {
        $this->validate();
    }

    /**
     * @throws InvalidArgumentException When value negative or zero.
     */
    private function validate(): void
    {
        if ($this->value <= 0) {
            throw new InvalidArgumentException('An identifier cant be negative or zero.');
        }
    }
}
