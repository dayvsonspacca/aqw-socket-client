<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use InvalidArgumentException;

final class Health
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
}
