<?php

declare(strict_types=1);

namespace AqwSocketClient\Clients;

use AqwSocketClient\Interfaces\ClientInterface;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Interfaces\ScriptInterface;
use Override;

abstract class AbstractClient implements ClientInterface
{
    #[Override]
    public function run(ScriptInterface $script): void
    {
        while ($this->isConnected() && !$script->isDone()) {
            foreach ($this->receive() as $message) {
                $this->processMessage($script, $message);
            }
        }
    }

    private function processMessage(ScriptInterface $script, MessageInterface $message): void
    {
        foreach ($this->resolveEvents($script, $message) as $event) {
            $this->dispatchEvent($script, $event);
        }
    }

    private function resolveEvents(ScriptInterface $script, MessageInterface $message): array
    {
        $events = [];

        foreach ($script->handles() as $eventClass) {
            $event = $eventClass::from($message);

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
