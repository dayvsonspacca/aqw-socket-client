<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Interfaces\EventInterface;

/**
 * Represents an event triggered after the player's inventory data has been
 * successfully loaded from the server.
 *
 * This event provides the list of items currently held by the player.
 */
class PlayerInventoryLoadedEvent implements EventInterface
{
    /**
     * @param array $items An array containing the player's inventory items.
     */
    public function __construct(
        public readonly array $items
    ) {
    }
}
