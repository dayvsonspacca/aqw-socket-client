<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Names;

use InvalidArgumentException;
use Override;
use Stringable;

abstract class Name implements Stringable
{
    public function __construct(
        public readonly string $value,
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        if ($this->value === '') {
            throw new InvalidArgumentException('A name cant be empty');
        }
    }

    #[Override]
    public function __toString(): string
    {
        return $this->value;
    }
}
