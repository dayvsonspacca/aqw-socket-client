<?php

declare(strict_types=1);

namespace AqwSocketClient\Clients;

use AqwSocketClient\Interfaces\ClientInterface;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\ExpirableScriptInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Interfaces\ScriptInterface;
use Override;

abstract class AbstractClient implements ClientInterface
{
    #[Override]
    public function run(ScriptInterface $script): void
    {
        while ($this->isConnected() && !$script->isDone()) {
            if ($script instanceof ExpirableScriptInterface && $script->isExpired()) {
                $script->expired();
                break;
            }

            foreach ($this->receive() as $message) {
                $this->processMessage($script, $message);
            }
        }

        if (!$this->isConnected()) {
            $script->disconnected();
        }
    }

    private function processMessage(ScriptInterface $script, MessageInterface $message): void
    {
        foreach ($this->resolveEvents($script, $message) as $event) {
            $this->dispatchEvent($script, $event);
        }
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

    private function dispatchEvent(ScriptInterface $script, EventInterface $event): void
    {
        foreach ($script->handle($event) as $command) {
            $this->send($command->pack());
        }
    }
}
