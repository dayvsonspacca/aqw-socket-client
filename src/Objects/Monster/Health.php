<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Monster;

use Override;
use Psl;
use Stringable;

final readonly class Health implements Stringable
{
    public function __construct(
        public readonly int $value,
    ) {
        Psl\invariant($this->value >= 0, 'Health cannot be negative, got %d.', $this->value);
    }

    #[Override]
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
