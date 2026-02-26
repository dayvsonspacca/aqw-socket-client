<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use AqwSocketClient\Commands\LoadPlayerInventoryCommand;
use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interpreters\AreaInterpreter;
use AqwSocketClient\Interpreters\AuthenticationInterpreter;
use AqwSocketClient\Interpreters\PlayerInterpreter;
use AqwSocketClient\Objects\AreaIdentifier;
use AqwSocketClient\Objects\SocketIdentifier;
use AqwSocketClient\Translators\AuthenticationTranslator;
use Override;

final class LoginScript extends AbstractScript
{
    private ?SocketIdentifier $socketId = null;
    private ?AreaIdentifier $areaId = null;

    public function __construct(
        private readonly string $username,
        #[\SensitiveParameter]
        private readonly string $token,
    ) {}

    #[Override]
    public function interpreters(): array
    {
        return [
            new AuthenticationInterpreter(),
            new AreaInterpreter(),
            new PlayerInterpreter(),
        ];
    }

    #[Override]
    public function translators(): array
    {
        return [new AuthenticationTranslator($this->username, $this->token)];
    }

    #[Override]
    public function handle(EventInterface $event): array
    {
        if ($event instanceof LoginRespondedEvent && $event->success) {
            $this->socketId = $event->socketId;
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
