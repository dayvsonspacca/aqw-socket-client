<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Interfaces\EventInterface;

class PlayerInventoryLoadedEvent implements EventInterface
{
    public function __construct(
        public readonly array $items
    ) {
    }
}
