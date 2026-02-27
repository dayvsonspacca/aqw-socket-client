<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Names;

use InvalidArgumentException;

abstract class Name
{
    public function __construct(
        public readonly string $value,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if ($this->value === '') {
            throw new InvalidArgumentException('A name cant be empty');
        }
    }
}
