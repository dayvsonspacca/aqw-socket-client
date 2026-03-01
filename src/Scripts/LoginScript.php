<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use AqwSocketClient\Commands\JoinInitialAreaCommand;
use AqwSocketClient\Commands\LoadPlayerInventoryCommand;
use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use AqwSocketClient\Objects\Names\PlayerName;
use Override;

final class LoginScript extends AbstractScript
{
    private ?SocketIdentifier $socketId = null;
    private ?AreaIdentifier $areaId = null;

    public function __construct(
        private readonly PlayerName $playerName,
        #[\SensitiveParameter]
        private readonly string $token,
    ) {}

    #[Override]
    public function handles(): array
    {
        return [
            ConnectionEstablishedEvent::class,
            LoginRespondedEvent::class,
            AreaJoinedEvent::class,
            PlayerInventoryLoadedEvent::class,
        ];
    }

    #[Override]
    public function handle(EventInterface $event): array
    {
        if ($event instanceof ConnectionEstablishedEvent) {
            return [new LoginCommand($this->playerName, $this->token)];
        }

        if ($event instanceof LoginRespondedEvent && $event->success) {
            $this->socketId = $event->socketId;
            return [new JoinInitialAreaCommand()];
        }

        if ($event instanceof AreaJoinedEvent && $event->mapName === 'battleon') {
            $this->areaId = $event->areaId;

            if ($this->socketId !== null) {
                $this->done();
                return [new LoadPlayerInventoryCommand($this->areaId, $this->socketId)];
            }
        }

        return [];
    }
}
