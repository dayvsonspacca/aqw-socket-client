<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use AqwSocketClient\Enums\ScriptResult;
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
    private ?ScriptResult $result = null;

    #[Override]
    public function isDone(): bool
    {
        return $this->done;
    }

    /**
     * Defaults to {@see AqwSocketClient\Enums\ScriptResult::Failed} when result not set.
     */
    #[Override]
    public function result(): ScriptResult
    {
        if ($this->result === null || !$this->isDone()) {
            return ScriptResult::Failed;
        }

        return $this->result;
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

    protected function setResult(ScriptResult $result): void
    {
        $this->result = $result;
    }

    #[Override]
    public function failed(): void
    {
        $this->done();
        $this->result = ScriptResult::Failed;
    }

    #[Override]
    public function disconnected(): void
    {
        $this->done();
        $this->result = ScriptResult::Disconnected;
    }

    #[Override]
    public function success(): void
    {
        $this->done();
        $this->result = ScriptResult::Success;
    }
}
