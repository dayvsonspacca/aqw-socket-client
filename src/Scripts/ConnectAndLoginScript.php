<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Objects\Names\PlayerName;
use Override;

final class ConnectAndLoginScript extends AbstractScript
{
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
        ];
    }

    #[Override]
    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface
    {
        if ($event instanceof ConnectionEstablishedEvent) {
            return new LoginCommand($this->playerName, $this->token);
        }

        if ($event instanceof LoginRespondedEvent) {
            if (!$event->success) {
                $this->failed();
                return null;
            }

            $context->set('socket_id', $event->socketId);
            $this->success();
        }

        return null;
    }
}
