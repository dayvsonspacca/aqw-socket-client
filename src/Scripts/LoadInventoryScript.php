<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use AqwSocketClient\Commands\LoadPlayerInventoryCommand;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use Override;
use Psl\Type;

final class LoadInventoryScript extends AbstractScript
{
    #[Override]
    public function start(ClientContext $context): ?CommandInterface
    {
        $socketId = Type\instance_of(SocketIdentifier::class)->assert($context->get('socket_id'));
        $areaId = Type\instance_of(AreaIdentifier::class)->assert($context->get('area_id'));

        return new LoadPlayerInventoryCommand($areaId, $socketId);
    }

    #[Override]
    public function handles(): array
    {
        return [PlayerInventoryLoadedEvent::class];
    }

    #[Override]
    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface
    {
        if ($event instanceof PlayerInventoryLoadedEvent) {
            $this->success();
        }

        return null;
    }
}
