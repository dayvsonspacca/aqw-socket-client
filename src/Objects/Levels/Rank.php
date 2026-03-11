<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Levels;

use Psl;

final readonly class Rank extends Level
{
    public function __construct(int $value)
    {
        parent::__construct($value);

        Psl\invariant($this->value <= 10, 'Rank cannot be greater than 10, got %d.', $this->value);
    }
}
