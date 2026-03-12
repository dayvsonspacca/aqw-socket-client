<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use AqwSocketClient\Commands\JoinInitialAreaCommand;
use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\EventInterface;
use Override;

final class JoinBattleonScript extends AbstractScript
{
    #[Override]
    public function start(ClientContext $context): ?CommandInterface
    {
        return new JoinInitialAreaCommand();
    }

    #[Override]
    public function handles(): array
    {
        return [AreaJoinedEvent::class];
    }

    #[Override]
    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface
    {
        if ($event instanceof AreaJoinedEvent && $event->area->name->value === 'battleon') {
            $context->set('area_id', $event->area->identifier);
            $this->success();
        }

        return null;
    }
}
