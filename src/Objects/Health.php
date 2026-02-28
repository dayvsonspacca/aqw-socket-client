<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use InvalidArgumentException;
use Override;
use Stringable;

final class Health implements Stringable
{
    public function __construct(
        public readonly int $value,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if ($this->value < 0) {
            throw new InvalidArgumentException('A health cant be negative');
        }
    }

    #[Override]
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
