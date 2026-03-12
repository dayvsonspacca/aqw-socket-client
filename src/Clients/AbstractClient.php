<?php

declare(strict_types=1);

namespace AqwSocketClient\Clients;

use AqwSocketClient\Interfaces\ClientInterface;
use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\ExpirableScriptInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Interfaces\ScriptInterface;
use AqwSocketClient\Scripts\ClientContext;
use Override;
use Psl\DataStructure\Queue;

abstract class AbstractClient implements ClientInterface
{
    #[Override]
    public function run(ScriptInterface $script): void
    {
        /** @var Queue<CommandInterface> $queue */
        $queue = new Queue();
        $context = new ClientContext();

        $initial = $script->start($context);

        if ($initial !== null) {
            $queue->enqueue($initial);
        }

        while ($this->isConnected() && !$script->isDone()) {
            if ($script instanceof ExpirableScriptInterface && $script->isExpired()) {
                $script->expired();
                break;
            }

            if ($queue->count() > 0) {
                $this->send($queue->dequeue()->pack());
            }

            foreach ($this->receive() as $message) {
                $command = $this->processMessage($script, $message, $context);

                if ($command !== null) {
                    $queue->enqueue($command);
                }
            }
        }

        if (!$this->isConnected()) {
            $script->disconnected();
        }
    }

    private function processMessage(
        ScriptInterface $script,
        MessageInterface $message,
        ClientContext $context,
    ): ?CommandInterface {
        foreach ($this->resolveEvents($script, $message) as $event) {
            $command = $script->handle($event, $context);

            if ($command !== null) {
                return $command;
            }
        }

        return null;
    }

    /**
     * @return EventInterface[]
     */
    private function resolveEvents(ScriptInterface $script, MessageInterface $message): array
    {
        $events = [];

        foreach ($script->handles() as $eventClass) {
            // @mago-expect analyzer:possibly-static-access-on-interface
            $event = $eventClass::from($message);
            /** @var null|EventInterface */
            if ($event !== null) {
                $events[] = $event;
            }
        }

        return $events;
    }
}
