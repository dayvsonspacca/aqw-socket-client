<?php

declare(strict_types=1);

namespace AqwSocketClient\Messages;

use AqwSocketClient\Enums\JsonCommandType;

class JsonCommand
{
    public function __construct(
        public readonly JsonCommandType $type,
        public readonly array $data
    ) {}
}
