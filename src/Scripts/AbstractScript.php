<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\InterpreterInterface;
use AqwSocketClient\Interfaces\ScriptInterface;
use Override;

/**
 * Base implementation for atomic scripts.
 *
 * Provides sensible defaults so concrete scripts only override
 * what they actually need. Subclasses signal completion by
 * calling {@see AqwSocketClient\Scripts\AbstractScript::done()} from within {@see AqwSocketClient\Interfaces\ScriptInterface::handle()}.
 */
abstract class AbstractScript implements ScriptInterface
{
    private bool $done = false;

    /** @return InterpreterInterface[] */
    #[Override]
    abstract public function interpreters(): array;

    /** @return CommandInterface[] */
    #[Override]
    abstract public function handle(EventInterface $event): array;

    #[Override]
    public function isDone(): bool
    {
        return $this->done;
    }

    /**
     * Marks this script as completed.
     *
     * Should be called from within {@see AqwSocketClient\Interfaces\ScriptInterface::handle()} once the script
     * has achieved its goal.
     */
    protected function done(): void
    {
        $this->done = true;
    }
}
