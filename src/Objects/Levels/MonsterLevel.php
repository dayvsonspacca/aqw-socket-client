<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Levels;

use InvalidArgumentException;
use Override;

final class MonsterLevel extends Level
{
    #[Override]
    protected function validate(): void
    {
        parent::validate();

        if ($this->value > 255) {
            throw new InvalidArgumentException('Monster level cannot be greater than 255.');
        }
    }
}
