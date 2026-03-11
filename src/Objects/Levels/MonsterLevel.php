<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Levels;

use Psl;

final readonly class MonsterLevel extends Level
{
    public function __construct(int $value)
    {
        parent::__construct($value);

        Psl\invariant($this->value <= 255, 'Monster level cannot be greater than 255, got %d.', $this->value);
    }
}
