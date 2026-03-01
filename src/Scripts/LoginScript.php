<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use AqwSocketClient\Commands\JoinInitialAreaCommand;
use AqwSocketClient\Commands\LoadPlayerInventoryCommand;
use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\ExpirableScriptInterface;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Scripts\Traits\HasExpiration;
use Override;
use RuntimeException;

final class LoginScript extends AbstractScript implements ExpirableScriptInterface
{
    use HasExpiration;

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
        ];
    }

    #[Override]
    public function handle(EventInterface $event): array
    {
        if ($event instanceof ConnectionEstablishedEvent) {
            return [new LoginCommand($this->playerName, $this->token)];
        }

        if ($event instanceof LoginRespondedEvent) {
            if ($event->success) {
                $this->socketId = $event->socketId;
                return [new JoinInitialAreaCommand()];
            }

            throw new RuntimeException('Failed to log-in');
        }

        if ($event instanceof AreaJoinedEvent && $event->area->name->value === 'battleon') {
            $this->areaId = $event->area->identifier;

            if ($this->socketId !== null) {
                $this->done();
                return [new LoadPlayerInventoryCommand($this->areaId, $this->socketId)];
            }
        }

        return [];
    }
}
