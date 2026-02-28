<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Identifiers;

use InvalidArgumentException;
use Override;
use Stringable;

abstract class Identifier implements Stringable
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
     * @throws InvalidArgumentException
     */
    protected function validate(): void
    {
        if ($this->value <= 0) {
            throw new InvalidArgumentException('An identifier cant be negative or zero.');
        }
    }

    #[Override]
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
