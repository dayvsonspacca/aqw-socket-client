<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Levels;

use Override;
use Psl\Type;
use Stringable;

abstract readonly class Level implements Stringable
{
    public function __construct(
        public readonly int $value,
    ) {
        Type\positive_int()->assert($this->value);
    }

    #[Override]
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
