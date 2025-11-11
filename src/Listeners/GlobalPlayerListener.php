<?php

declare(strict_types=1);

namespace AqwSocketClient\Listeners;

use AqwSocketClient\Events\{JoinedAreaEvent, LoginResponseEvent};
use AqwSocketClient\Interfaces\{EventInterface, ListenerInterface};

/**
 * A listener responsible for updating and maintaining global state information
 * related to the player's connection and location, such as the current socket ID and area ID.
 *
 * @see AqwSocketClient\Interfaces\ListenerInterface
 */
class GlobalPlayerListener implements ListenerInterface
{
    /**
     * @var int The **temporary socket ID** assigned to the client by the server
     * upon successful connection/login.
     */
    public int $socketId;

    /**
     * @var int The ID of the current screen or **area** the player is in within a map.
     */
    public int $areaId;

    /**
     * Executes logic based on the received event, specifically updating the
     * internal $socketId upon a successful {@see AqwSocketClient\Events\LoginResponseEvent}
     * and the $areaId upon a {@see AqwSocketClient\Events\JoinedAreaEvent}.
     *
     * @param EventInterface $event The interpreted event object to be processed.
     * @return void
     */
    public function listen(EventInterface $event)
    {
        if ($event instanceof LoginResponseEvent) {
            $this->socketId = $event->socketId;
        }

        if ($event instanceof JoinedAreaEvent) {
            $this->areaId = $event->areaId;
        }
    }
}