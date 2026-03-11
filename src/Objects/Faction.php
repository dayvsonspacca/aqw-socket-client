<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use AqwSocketClient\Objects\Identifiers\FactionIdentifier;
use AqwSocketClient\Objects\Names\FactionName;

final readonly class Faction
{
    public function __construct(
        public readonly FactionIdentifier $identifier,
        public readonly FactionName $name,
    ) {}
}
