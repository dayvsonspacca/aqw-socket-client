<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Levels;

use InvalidArgumentException;
use Override;
use Stringable;

abstract class Level implements Stringable
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

    #[Override]
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
