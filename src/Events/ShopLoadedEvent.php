<?php


declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Objects\Shop;

/**
 * Represents an event triggered when the client receives the full data set
 * after requesting a specific shop in the game.
 *
 * This event contains all the meta-information and the list of items available
 * in the loaded shop.
 */
class ShopLoadedEvent implements EventInterface
{
    public function __construct(
        public readonly Shop $shop
    ) {
    }
}
