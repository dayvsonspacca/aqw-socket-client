<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Names;

use Override;
use Psl\Type;
use Stringable;

abstract readonly class Name implements Stringable
{
    public function __construct(
        public readonly string $value,
    ) {
        Type\non_empty_string()->assert($this->value);
    }

    #[Override]
    public function __toString(): string
    {
        return $this->value;
    }
}
