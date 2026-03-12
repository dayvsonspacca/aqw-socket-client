<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\ExpirableScriptInterface;
use AqwSocketClient\Interfaces\ScriptInterface;
use Override;

/**
 * Runs a list of scripts in order.
 *
 * Advances to the next script when the current one completes with Success.
 * Fails immediately if any child fails, disconnects, or expires.
 */
class SequenceScript extends AbstractScript
{
    private int $current = 0;

    /**
     * @param ScriptInterface[] $scripts
     */
    public function __construct(
        private readonly array $scripts,
    ) {}

    #[Override]
    public function start(ClientContext $context): ?CommandInterface
    {
        if ($this->scripts === []) {
            $this->success();
            return null;
        }

        return $this->scripts[0]->start($context);
    }

    #[Override]
    public function handles(): array
    {
        return $this->scripts[$this->current]->handles();
    }

    #[Override]
    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface
    {
        $child = $this->scripts[$this->current];

        if ($child instanceof ExpirableScriptInterface && $child->isExpired()) {
            $child->expired();
            $this->failed();
            return null;
        }

        $command = $child->handle($event, $context);

        if ($child->isDone()) {
            if ($child->result() !== ScriptResult::Success) {
                $this->failed();
                return null;
            }

            $this->current++;

            if ($this->current >= count($this->scripts)) {
                $this->success();
                return null;
            }

            return $this->scripts[$this->current]->start($context);
        }

        return $command;
    }
}
