<?php

declare(strict_types=1);

namespace AqwSocketClient\Listeners;

use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\ListenerInterface;
use Override;

/**
 * Listens to player-related events, useful for maintaining the current state of the player's information.
 *
 * ### Events:
 * - {@see AqwSocketClient\Events\LoginRespondedEvent}
 * - {@see AqwSocketClient\Events\AreaJoinedEvent}
 */
final class GlobalPlayerListener implements ListenerInterface
{
    /**
     * @var ?int The **temporary socket ID** assigned to the client by the server
     * upon successful connection/login.
     */
    public ?int $socketId = null;

    /**
     * @var ?int The ID of the current screen or **area** the player is in within a map.
     */
    public ?int $areaId = null;

    #[Override]
    public function listen(EventInterface $event): void
    {
        if ($event instanceof LoginRespondedEvent) {
            $this->socketId = $event->socketId;
        }

        if ($event instanceof AreaJoinedEvent) {
            $this->areaId = $event->areaId;
        }
    }
}
