<?php 

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Interfaces\EventInterface;

class ShopLoadedEvent implements EventInterface
{
    public function __construct(
        public readonly int $shopId,
        public readonly string $shopName,
        public readonly bool $isUpgrade,
        public readonly bool $isHouseShop,
        public readonly array $items
    ) {
    }
}
