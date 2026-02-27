<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Levels;

use InvalidArgumentException;

abstract class Level
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        public readonly int $value,
    ) {
        $this->validate();
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function validate(): void
    {
        if ($this->value <= 0) {
            throw new InvalidArgumentException('A level cant be negative or zero.');
        }
    }
}
