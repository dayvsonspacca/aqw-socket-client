<?php 

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Interfaces\EventInterface;

/**
 * Represents an event triggered when the client receives the full data set
 * after requesting a specific shop in the game.
 *
 * This event contains all the meta-information and the list of items available
 * in the loaded shop.
 */
class ShopLoadedEvent implements EventInterface
{
    /**
     * @param int $shopId The unique identifier for the shop.
     * @param string $shopName The display name of the shop (e.g., 'A Shop').
     * @param bool $isUpgrade Indicates if this shop requires the player to have an active Membership.
     * @param bool $isHouseShop Indicates if this shop is a house related shop.
     * @param array<int, array{id: string, name: string, description: string}> $items A list of items available for purchase in this shop, including their ID, name, and description.
     */
    public function __construct(
        public readonly int $shopId,
        public readonly string $shopName,
        public readonly bool $isUpgrade,
        public readonly bool $isHouseShop,
        public readonly array $items
    ) {
    }
}