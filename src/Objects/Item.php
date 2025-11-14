<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

class Item
{
    public const COINS = 0;
    public const AC    = 1;

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $type,
        public readonly string $assetUrl,
        public readonly bool $memberOnly,
        public readonly int $coinType,
        public readonly int $coinAmount
    ) {
    }
}
