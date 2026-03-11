# Script Architecture Redesign Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the purely reactive script model with one that supports explicit initialization (`start()`), safe one-command-at-a-time dispatch, sequential composition (`SequenceScript`), and a fluent pipeline DSL (`Pipeline`).

**Architecture:** `ScriptInterface` gains a `start(ClientContext): ?CommandInterface` method and `handle()` narrows to `?CommandInterface`. A shared `ClientContext` (backed by `Psl\Collection\MutableMap`) is passed through every call. `SequenceScript` chains atomic scripts; `Pipeline` provides a builder API for simple linear flows. `AbstractClient` adds an internal command queue drained one-per-tick. `LoginScript` is decomposed into three atomic scripts orchestrated by `SequenceScript`.

**Tech Stack:** PHP 8.3, PHPUnit 11, PSL (azjezz/psl), mago (lint/analyze/fmt), composer scripts.

---

## Chunk 1: Infrastructure — ClientContext, ScriptInterface, AbstractScript, AbstractClient

### Task 1: `ClientContext`

**Files:**
- Create: `src/Scripts/ClientContext.php`
- Create: `tests/Unit/Scripts/ClientContextTest.php`

- [ ] **Step 1: Write the failing test**

```php
// tests/Unit/Scripts/ClientContextTest.php
<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Scripts;

use AqwSocketClient\Scripts\ClientContext;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ClientContextTest extends TestCase
{
    #[Test]
    public function it_returns_null_for_unknown_key(): void
    {
        $ctx = new ClientContext();
        $this->assertNull($ctx->get('missing'));
    }

    #[Test]
    public function it_stores_and_retrieves_a_value(): void
    {
        $ctx = new ClientContext();
        $ctx->set('foo', 'bar');
        $this->assertSame('bar', $ctx->get('foo'));
    }

    #[Test]
    public function it_overwrites_an_existing_value(): void
    {
        $ctx = new ClientContext();
        $ctx->set('foo', 'bar');
        $ctx->set('foo', 'baz');
        $this->assertSame('baz', $ctx->get('foo'));
    }

    #[Test]
    public function it_reports_key_presence(): void
    {
        $ctx = new ClientContext();
        $this->assertFalse($ctx->has('x'));
        $ctx->set('x', 42);
        $this->assertTrue($ctx->has('x'));
    }
}
```

- [ ] **Step 2: Run the test to verify it fails**

```bash
./vendor/bin/phpunit --filter ClientContextTest
```

Expected: FAIL — class not found.

- [ ] **Step 3: Implement `ClientContext`**

```php
// src/Scripts/ClientContext.php
<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use Psl\Collection\MutableMap;

/**
 * Mutable session-scoped state shared across all scripts in a run.
 *
 * Deliberately not a value object — it is mutable by design and does not
 * belong in src/Objects/. Each client run creates one instance and passes
 * it through every start() and handle() call.
 */
final class ClientContext
{
    /** @var MutableMap<string, mixed> */
    private MutableMap $data;

    public function __construct()
    {
        $this->data = new MutableMap([]);
    }

    public function set(string $key, mixed $value): void
    {
        if ($this->data->contains($key)) {
            $this->data->set($key, $value);
        } else {
            $this->data->add($key, $value);
        }
    }

    public function get(string $key): mixed
    {
        if (!$this->data->contains($key)) {
            return null;
        }

        return $this->data->get($key);
    }

    public function has(string $key): bool
    {
        return $this->data->contains($key);
    }
}
```

- [ ] **Step 4: Run the test to verify it passes**

```bash
./vendor/bin/phpunit --filter ClientContextTest
```

Expected: 4 tests, 4 assertions — all PASS.

- [ ] **Step 5: Lint, analyze, format**

```bash
./vendor/bin/mago lint src/Scripts/ClientContext.php
./vendor/bin/mago analyze src/Scripts/ClientContext.php
./vendor/bin/mago format src/Scripts/ClientContext.php
```

- [ ] **Step 6: Commit**

```bash
git add src/Scripts/ClientContext.php tests/Unit/Scripts/ClientContextTest.php
git commit -m "feat(scripts): add ClientContext for session-scoped state sharing"
```

---

### Task 2: Update `ScriptInterface`

**Files:**
- Modify: `src/Interfaces/ScriptInterface.php`

> No new test here — the interface change is validated by concrete implementations. The existing test suite will fail until `AbstractScript` is updated in Task 3.

- [ ] **Step 1: Add `start()` and update `handle()` signature**

```php
// src/Interfaces/ScriptInterface.php — replace the two method signatures:

/**
 * Called once by the client before the event loop begins.
 *
 * Returns the first command to send, or null if no immediate action is needed.
 * Default implementation in AbstractScript returns null.
 *
 * @param ClientContext $context Shared session state.
 */
public function start(ClientContext $context): ?CommandInterface;

/**
 * Handles an incoming event.
 *
 * Returns at most one command to send. The client queues it and sends it
 * on the next available tick.
 *
 * @param EventInterface $event The incoming event.
 * @param ClientContext  $context Shared session state.
 *
 * @return ?CommandInterface A command to dispatch, or null.
 */
public function handle(EventInterface $event, ClientContext $context): ?CommandInterface;
```

Also add `use AqwSocketClient\Scripts\ClientContext;` to the import block.

Full file after the edit:

```php
<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Scripts\ClientContext;

/**
 * Represents a single unit of logic to be executed against a {@see AqwSocketClient\Interfaces\ClientInterface}.
 *
 * Scripts are composable — a script can be a single atomic step or a
 * sequence of other scripts. The client drives the execution loop,
 * advancing to the next script only when the current one is done.
 */
interface ScriptInterface
{
    /**
     * Called once by the client before the event loop begins.
     *
     * Returns the first command to send, or null if no immediate action is needed.
     * Default implementation in AbstractScript returns null.
     *
     * @param ClientContext $context Shared session state.
     */
    public function start(ClientContext $context): ?CommandInterface;

    /**
     * Handles an incoming event.
     *
     * Returns at most one command to send. The client queues it and sends it
     * on the next available tick.
     *
     * @param EventInterface $event The incoming event.
     * @param ClientContext  $context Shared session state.
     *
     * @return ?CommandInterface A command to dispatch, or null.
     */
    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface;

    /**
     * Returns the list of event types this script is interested in.
     *
     * @return array<class-string<EventInterface>>
     */
    public function handles(): array;

    /**
     * Signals whether this script has completed its work.
     *
     * Checked by the client after every {@see AqwSocketClient\Interfaces\ScriptInterface::handle()} call.
     * When true, the client stops driving this script and moves on.
     */
    public function isDone(): bool;

    /**
     * Returns the final execution result of the script.
     *
     * Should only be relied upon once {@see AqwSocketClient\Interfaces\ScriptInterface::isDone()} returns true.
     */
    public function result(): ScriptResult;

    /**
     * Marks the script as failed.
     */
    public function failed(): void;

    /**
     * Marks the script as disconnected.
     */
    public function disconnected(): void;

    /**
     * Marks the script as successfully completed.
     */
    public function success(): void;
}
```

- [ ] **Step 2: Lint, analyze, format**

```bash
./vendor/bin/mago lint src/Interfaces/ScriptInterface.php
./vendor/bin/mago analyze src/Interfaces/ScriptInterface.php
./vendor/bin/mago format src/Interfaces/ScriptInterface.php
```

> **Note:** After this commit, `LoginScript` will be incompatible with the updated interface and the full test suite will be broken until Chunk 3 is complete. Do **not** run `composer quality` between Task 2 and the end of Task 10 — run only per-file mago checks and targeted `--filter` PHPUnit tests.

- [ ] **Step 3: Commit**

```bash
git add src/Interfaces/ScriptInterface.php
git commit -m "feat(interfaces): add start() and narrow handle() to ?CommandInterface"
```

---

### Task 3: Update `AbstractScript` and `ExpirableScript`

**Files:**
- Modify: `src/Scripts/AbstractScript.php`
- Modify: `src/Scripts/ExpirableScript.php`

- [ ] **Step 1: Add default `start()` to `AbstractScript`**

In `AbstractScript`, add after the class opening (before `isDone()`):

```php
use AqwSocketClient\Scripts\ClientContext;

// inside the class:

#[Override]
public function start(ClientContext $context): ?CommandInterface
{
    return null;
}
```

Also update the `handle()` docblock / signature if it was declared (it is abstract-implicit in this class — but `AbstractScript` doesn't declare `handle()` as it is left to concrete classes). Actually, `AbstractScript` does NOT declare `handle()` or `handles()` — those are left to concrete scripts. So no change needed there for `AbstractScript`.

The full updated `AbstractScript`:

```php
<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Interfaces\CommandInterface;
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
    public function start(ClientContext $context): ?CommandInterface
    {
        return null;
    }

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
```

`ExpirableScript` does not declare `handle()` or `start()` either — it only adds `expired()`, `expiresAt()`, and `isExpired()`. No changes needed there.

- [ ] **Step 2: Lint and analyze**

```bash
./vendor/bin/mago lint src/Scripts/AbstractScript.php
./vendor/bin/mago analyze src/Scripts/AbstractScript.php
```

- [ ] **Step 3: Run the test suite to see what's broken**

```bash
composer test
```

Expected: failures in `LoginScriptTest` and `SocketClientTest` because `LoginScript::handle()` still returns `array` and uses old signature. This is expected — we fix it in Chunk 3.

- [ ] **Step 4: Commit**

```bash
git add src/Scripts/AbstractScript.php
git commit -m "feat(scripts): add default start() to AbstractScript"
```

---

### Task 4: Update `AbstractClient` — command queue + context

**Files:**
- Modify: `src/Clients/AbstractClient.php`

The new loop:
1. Create `ClientContext`.
2. Call `script->start($context)`, enqueue result.
3. Each tick: check expiry → drain one command → receive messages → handle events → enqueue result.
4. Loop until `isDone()` or disconnected.

- [ ] **Step 1: Rewrite `AbstractClient::run()`**

```php
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
use SplQueue;

abstract class AbstractClient implements ClientInterface
{
    #[Override]
    public function run(ScriptInterface $script): void
    {
        /** @var SplQueue<CommandInterface> $queue */
        $queue = new SplQueue();
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

            if (!$queue->isEmpty()) {
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

    private function processMessage(ScriptInterface $script, MessageInterface $message, ClientContext $context): ?CommandInterface
    {
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
```

> **Note:** The original `AbstractClient` had `run()`, `processMessage()`, and `dispatchEvent()`. The new version replaces all three. `dispatchEvent()` is removed entirely — its responsibility (send command) is absorbed by the queue drain in `run()`. `processMessage()` now returns a command instead of sending it directly.

- [ ] **Step 2: Lint and analyze**

```bash
./vendor/bin/mago lint src/Clients/AbstractClient.php
./vendor/bin/mago analyze src/Clients/AbstractClient.php
```

- [ ] **Step 3: Verify the codebase compiles (expected failures only)**

```bash
composer test 2>&1 | head -40
```

Expected: failures only in `LoginScriptTest` and `SocketClientTest` (because `LoginScript` still uses the old `handle()` signature). No new failures in other test files. The `ClientContextTest`, `SequenceScriptTest`, `PipelineTest`, and atomic script tests all pass individually.

- [ ] **Step 4: Commit**

```bash
git add src/Clients/AbstractClient.php
git commit -m "feat(clients): add command queue and ClientContext to AbstractClient loop"
```

---

## Chunk 2: SequenceScript and Pipeline

### Task 5: `SequenceScript`

**Files:**
- Create: `src/Scripts/SequenceScript.php`
- Create: `tests/Unit/Scripts/SequenceScriptTest.php`

- [ ] **Step 1: Write the failing tests**

```php
// tests/Unit/Scripts/SequenceScriptTest.php
<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Scripts;

use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Scripts\AbstractScript;
use AqwSocketClient\Scripts\ClientContext;
use AqwSocketClient\Scripts\ExpirableScript;
use AqwSocketClient\Scripts\SequenceScript;
use DateTimeImmutable;
use Override;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

// --- Test doubles ---

final class StubEvent implements EventInterface
{
    public static function from(\AqwSocketClient\Interfaces\MessageInterface $message): ?EventInterface
    {
        return null;
    }
}

final class SucceedingScript extends AbstractScript
{
    public function handles(): array { return [StubEvent::class]; }

    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface
    {
        $this->success();
        return null;
    }
}

final class FailingScript extends AbstractScript
{
    public function handles(): array { return [StubEvent::class]; }

    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface
    {
        $this->failed();
        return null;
    }
}

final class WritingScript extends AbstractScript
{
    public function handles(): array { return [StubEvent::class]; }

    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface
    {
        $context->set('written', true);
        $this->success();
        return null;
    }
}

final class ReadingScript extends AbstractScript
{
    public bool $sawValue = false;

    public function handles(): array { return [StubEvent::class]; }

    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface
    {
        $this->sawValue = $context->has('written') && $context->get('written') === true;
        $this->success();
        return null;
    }
}

final class ExpirableStubScript extends ExpirableScript
{
    public function handles(): array { return [StubEvent::class]; }

    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface
    {
        return null;
    }
}

// --- Tests ---

final class SequenceScriptTest extends TestCase
{
    private ClientContext $ctx;

    protected function setUp(): void
    {
        $this->ctx = new ClientContext();
    }

    #[Test]
    public function it_succeeds_when_all_children_succeed(): void
    {
        $seq = new SequenceScript([new SucceedingScript(), new SucceedingScript()]);

        $event = new StubEvent();
        $seq->handle($event, $this->ctx);
        $seq->handle($event, $this->ctx);

        $this->assertTrue($seq->isDone());
        $this->assertSame(ScriptResult::Success, $seq->result());
    }

    #[Test]
    public function it_fails_immediately_when_a_child_fails(): void
    {
        $seq = new SequenceScript([new FailingScript(), new SucceedingScript()]);

        $seq->handle(new StubEvent(), $this->ctx);

        $this->assertTrue($seq->isDone());
        $this->assertSame(ScriptResult::Failed, $seq->result());
    }

    #[Test]
    public function it_delegates_handles_to_active_child(): void
    {
        $seq = new SequenceScript([new SucceedingScript(), new FailingScript()]);

        $this->assertSame([StubEvent::class], $seq->handles());
    }

    #[Test]
    public function it_passes_context_between_children(): void
    {
        $writer = new WritingScript();
        $reader = new ReadingScript();

        $seq = new SequenceScript([$writer, $reader]);
        $event = new StubEvent();

        $seq->handle($event, $this->ctx); // writer succeeds, advances
        $seq->handle($event, $this->ctx); // reader runs

        $this->assertTrue($reader->sawValue);
    }

    #[Test]
    public function it_fails_when_active_child_expires(): void
    {
        $expirable = new ExpirableStubScript();
        $expirable->expiresAt(new DateTimeImmutable('-10 seconds'));

        $seq = new SequenceScript([$expirable]);
        $seq->handle(new StubEvent(), $this->ctx);

        $this->assertTrue($seq->isDone());
        $this->assertSame(ScriptResult::Failed, $seq->result());
    }
}
```

- [ ] **Step 2: Run to verify failure**

```bash
./vendor/bin/phpunit --filter SequenceScriptTest
```

Expected: FAIL — class not found.

- [ ] **Step 3: Implement `SequenceScript`**

```php
// src/Scripts/SequenceScript.php
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
final class SequenceScript extends AbstractScript
{
    private int $current = 0;

    /**
     * @param ScriptInterface[] $scripts
     */
    public function __construct(private readonly array $scripts) {}

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
```

- [ ] **Step 4: Run tests to verify they pass**

```bash
./vendor/bin/phpunit --filter SequenceScriptTest
```

Expected: 5 tests — all PASS.

- [ ] **Step 5: Lint, analyze, format**

```bash
./vendor/bin/mago lint src/Scripts/SequenceScript.php
./vendor/bin/mago analyze src/Scripts/SequenceScript.php
./vendor/bin/mago format src/Scripts/SequenceScript.php
```

- [ ] **Step 6: Commit**

```bash
git add src/Scripts/SequenceScript.php tests/Unit/Scripts/SequenceScriptTest.php
git commit -m "feat(scripts): add SequenceScript for ordered script composition"
```

---

### Task 6: `Pipeline`

**Files:**
- Create: `src/Scripts/Pipeline.php`
- Create: `tests/Unit/Scripts/PipelineTest.php`

- [ ] **Step 1: Write the failing tests**

```php
// tests/Unit/Scripts/PipelineTest.php
<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Scripts;

use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Scripts\ClientContext;
use AqwSocketClient\Scripts\Pipeline;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

// --- Test doubles ---

final class SuccessEvent implements EventInterface
{
    public static function from(MessageInterface $message): ?EventInterface { return null; }
}

final class FailureEvent implements EventInterface
{
    public static function from(MessageInterface $message): ?EventInterface { return null; }
}

final class UnrelatedEvent implements EventInterface
{
    public static function from(MessageInterface $message): ?EventInterface { return null; }
}

final class StubCommand implements CommandInterface
{
    public function pack(): \AqwSocketClient\Packet
    {
        return \AqwSocketClient\Packet::packetify('test', []);
    }
}

// --- Tests ---

final class PipelineTest extends TestCase
{
    private ClientContext $ctx;

    protected function setUp(): void
    {
        $this->ctx = new ClientContext();
    }

    #[Test]
    public function it_emits_initial_command_on_start(): void
    {
        $cmd = new StubCommand();
        $pipeline = Pipeline::send($cmd)->waitFor(SuccessEvent::class);

        $result = $pipeline->start($this->ctx);

        $this->assertSame($cmd, $result);
    }

    #[Test]
    public function it_succeeds_when_expected_event_arrives(): void
    {
        $pipeline = Pipeline::send(new StubCommand())->waitFor(SuccessEvent::class);

        $pipeline->handle(new SuccessEvent(), $this->ctx);

        $this->assertTrue($pipeline->isDone());
        $this->assertSame(ScriptResult::Success, $pipeline->result());
    }

    #[Test]
    public function it_stores_matched_event_on_success(): void
    {
        $pipeline = Pipeline::send(new StubCommand())->waitFor(SuccessEvent::class);
        $event = new SuccessEvent();

        $pipeline->handle($event, $this->ctx);

        $this->assertSame($event, $pipeline->matchedEvent);
    }

    #[Test]
    public function it_fails_when_failure_event_arrives(): void
    {
        $pipeline = Pipeline::send(new StubCommand())
            ->waitFor(SuccessEvent::class)
            ->orFailOn(FailureEvent::class);

        $pipeline->handle(new FailureEvent(), $this->ctx);

        $this->assertTrue($pipeline->isDone());
        $this->assertSame(ScriptResult::Failed, $pipeline->result());
    }

    #[Test]
    public function it_stores_matched_event_on_failure(): void
    {
        $pipeline = Pipeline::send(new StubCommand())
            ->waitFor(SuccessEvent::class)
            ->orFailOn(FailureEvent::class);
        $event = new FailureEvent();

        $pipeline->handle($event, $this->ctx);

        $this->assertSame($event, $pipeline->matchedEvent);
    }

    #[Test]
    public function it_ignores_unrelated_events(): void
    {
        $pipeline = Pipeline::send(new StubCommand())->waitFor(SuccessEvent::class);

        $pipeline->handle(new UnrelatedEvent(), $this->ctx);

        $this->assertFalse($pipeline->isDone());
    }

    #[Test]
    public function it_invokes_callback_on_success_and_returns_its_command(): void
    {
        $followUp = new StubCommand();
        $pipeline = Pipeline::send(new StubCommand())
            ->waitFor(SuccessEvent::class, fn(SuccessEvent $e, ClientContext $ctx): ?CommandInterface => $followUp);

        $returned = $pipeline->handle(new SuccessEvent(), $this->ctx);

        $this->assertSame($followUp, $returned);
    }

    #[Test]
    public function it_declares_all_registered_event_classes_in_handles(): void
    {
        $pipeline = Pipeline::send(new StubCommand())
            ->waitFor(SuccessEvent::class)
            ->orFailOn(FailureEvent::class);

        $handles = $pipeline->handles();

        $this->assertContains(SuccessEvent::class, $handles);
        $this->assertContains(FailureEvent::class, $handles);
    }

    #[Test]
    public function it_writes_to_context_from_callback(): void
    {
        $pipeline = Pipeline::send(new StubCommand())
            ->waitFor(SuccessEvent::class, function (SuccessEvent $e, ClientContext $ctx): ?CommandInterface {
                $ctx->set('key', 'value');
                return null;
            });

        $pipeline->handle(new SuccessEvent(), $this->ctx);

        $this->assertSame('value', $this->ctx->get('key'));
    }
}
```

- [ ] **Step 2: Run to verify failure**

```bash
./vendor/bin/phpunit --filter PipelineTest
```

Expected: FAIL — class not found.

- [ ] **Step 3: Implement `Pipeline`**

```php
// src/Scripts/Pipeline.php
<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\EventInterface;
use Closure;
use Override;

/**
 * Fluent DSL for simple linear event flows.
 *
 * Use Pipeline::send() to create a pipeline. Chain waitFor() and orFailOn()
 * to declare the expected success and failure events.
 *
 * Pipeline and class-based scripts are interchangeable inside SequenceScript.
 */
final class Pipeline extends AbstractScript
{
    public ?EventInterface $matchedEvent = null;

    /** @var class-string<EventInterface> */
    private string $successClass;

    /** @var Closure(EventInterface, ClientContext): ?CommandInterface|null */
    private ?Closure $successCallback = null;

    /** @var list<class-string<EventInterface>> */
    private array $failureClasses = [];

    private function __construct(private readonly CommandInterface $initialCommand) {}

    public static function send(CommandInterface $command): self
    {
        return new self($command);
    }

    /**
     * @template T of EventInterface
     * @param class-string<T> $eventClass
     * @param (Closure(T, ClientContext): ?CommandInterface)|null $callback
     */
    public function waitFor(string $eventClass, ?Closure $callback = null): self
    {
        $this->successClass = $eventClass;
        $this->successCallback = $callback;
        return $this;
    }

    /**
     * @param class-string<EventInterface> $eventClass
     */
    public function orFailOn(string $eventClass): self
    {
        $this->failureClasses[] = $eventClass;
        return $this;
    }

    #[Override]
    public function start(ClientContext $context): ?CommandInterface
    {
        return $this->initialCommand;
    }

    #[Override]
    public function handles(): array
    {
        return [$this->successClass, ...$this->failureClasses];
    }

    #[Override]
    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface
    {
        if ($event instanceof $this->successClass) {
            $this->matchedEvent = $event;
            $command = null;

            if ($this->successCallback !== null) {
                $command = ($this->successCallback)($event, $context);
            }

            $this->success();
            return $command;
        }

        foreach ($this->failureClasses as $failureClass) {
            if ($event instanceof $failureClass) {
                $this->matchedEvent = $event;
                $this->failed();
                return null;
            }
        }

        return null;
    }
}
```

- [ ] **Step 4: Run tests to verify they pass**

```bash
./vendor/bin/phpunit --filter PipelineTest
```

Expected: 9 tests — all PASS.

- [ ] **Step 5: Lint, analyze, format**

```bash
./vendor/bin/mago lint src/Scripts/Pipeline.php
./vendor/bin/mago analyze src/Scripts/Pipeline.php
./vendor/bin/mago format src/Scripts/Pipeline.php
```

- [ ] **Step 6: Commit**

```bash
git add src/Scripts/Pipeline.php tests/Unit/Scripts/PipelineTest.php
git commit -m "feat(scripts): add Pipeline DSL for linear event flows"
```

---

## Chunk 3: LoginScript Migration and Test Updates

### Task 7: `ConnectAndLoginScript` (atomic step 1)

Handles `ConnectionEstablishedEvent` (sends `LoginCommand`) and `LoginRespondedEvent` (stores socketId in context, returns `JoinInitialAreaCommand` — but does NOT call success here; `JoinBattleonScript` succeeds when the area is joined).

Wait — actually each atomic script should be responsible for one "step". Let me redefine:

- `ConnectAndLoginScript`: handles `ConnectionEstablishedEvent` + `LoginRespondedEvent`. On `ConnectionEstablished` → sends `LoginCommand`. On `LoginResponded(success)` → stores socketId in context, **calls success()**. On `LoginResponded(fail)` → `failed()`.
- `JoinBattleonScript`: `start()` → sends `JoinInitialAreaCommand`. Handles `AreaJoinedEvent` for 'battleon' → stores areaId in context, success().
- `LoadInventoryScript`: `start()` → reads socketId + areaId from context, sends `LoadPlayerInventoryCommand`. Handles `PlayerInventoryLoadedEvent` → success().

**Files:**
- Create: `src/Scripts/ConnectAndLoginScript.php`
- Create: `tests/Unit/Scripts/ConnectAndLoginScriptTest.php`

- [ ] **Step 1: Write the failing test**

```php
// tests/Unit/Scripts/ConnectAndLoginScriptTest.php
<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Scripts;

use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Scripts\ClientContext;
use AqwSocketClient\Scripts\ConnectAndLoginScript;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConnectAndLoginScriptTest extends TestCase
{
    private ClientContext $ctx;
    private ConnectAndLoginScript $script;

    protected function setUp(): void
    {
        $this->ctx = new ClientContext();
        $this->script = new ConnectAndLoginScript(new PlayerName('Hilise'), 'token');
    }

    #[Test]
    public function it_returns_login_command_on_connection_established(): void
    {
        $command = $this->script->handle(new ConnectionEstablishedEvent(), $this->ctx);
        $this->assertInstanceOf(LoginCommand::class, $command);
    }

    #[Test]
    public function it_succeeds_and_stores_socket_id_on_successful_login(): void
    {
        $socketId = new SocketIdentifier(42);
        $this->script->handle(new LoginRespondedEvent(true, $socketId), $this->ctx);

        $this->assertTrue($this->script->isDone());
        $this->assertSame(ScriptResult::Success, $this->script->result());
        $this->assertSame($socketId, $this->ctx->get('socket_id'));
    }

    #[Test]
    public function it_fails_on_failed_login(): void
    {
        $this->script->handle(new LoginRespondedEvent(false, null), $this->ctx);

        $this->assertTrue($this->script->isDone());
        $this->assertSame(ScriptResult::Failed, $this->script->result());
    }

    #[Test]
    public function it_returns_null_on_successful_login_response(): void
    {
        $command = $this->script->handle(new LoginRespondedEvent(true, new SocketIdentifier(1)), $this->ctx);
        $this->assertNull($command);
    }
}
```

- [ ] **Step 2: Run to verify failure**

```bash
./vendor/bin/phpunit --filter ConnectAndLoginScriptTest
```

- [ ] **Step 3: Implement `ConnectAndLoginScript`**

```php
// src/Scripts/ConnectAndLoginScript.php
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
            if ($event->success) {
                $context->set('socket_id', $event->socketId);
                $this->success();
            } else {
                $this->failed();
            }
        }

        return null;
    }
}
```

- [ ] **Step 4: Run tests to verify they pass**

```bash
./vendor/bin/phpunit --filter ConnectAndLoginScriptTest
```

- [ ] **Step 5: Lint, analyze, format**

```bash
./vendor/bin/mago lint src/Scripts/ConnectAndLoginScript.php
./vendor/bin/mago analyze src/Scripts/ConnectAndLoginScript.php
./vendor/bin/mago format src/Scripts/ConnectAndLoginScript.php
```

- [ ] **Step 6: Commit**

```bash
git add src/Scripts/ConnectAndLoginScript.php tests/Unit/Scripts/ConnectAndLoginScriptTest.php
git commit -m "feat(scripts): add ConnectAndLoginScript (login sequence step 1)"
```

---

### Task 8: `JoinBattleonScript` (atomic step 2)

**Files:**
- Create: `src/Scripts/JoinBattleonScript.php`
- Create: `tests/Unit/Scripts/JoinBattleonScriptTest.php`

- [ ] **Step 1: Write the failing test**

```php
// tests/Unit/Scripts/JoinBattleonScriptTest.php
<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Scripts;

use AqwSocketClient\Commands\JoinInitialAreaCommand;
use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Objects\Area\Area;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\RoomIdentifier;
use AqwSocketClient\Objects\Names\AreaName;
use AqwSocketClient\Scripts\ClientContext;
use AqwSocketClient\Scripts\JoinBattleonScript;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class JoinBattleonScriptTest extends TestCase
{
    private ClientContext $ctx;
    private JoinBattleonScript $script;

    protected function setUp(): void
    {
        $this->ctx = new ClientContext();
        $this->script = new JoinBattleonScript();
    }

    #[Test]
    public function it_sends_join_initial_area_command_on_start(): void
    {
        $command = $this->script->start($this->ctx);
        $this->assertInstanceOf(JoinInitialAreaCommand::class, $command);
    }

    #[Test]
    public function it_succeeds_and_stores_area_id_when_battleon_is_joined(): void
    {
        $areaId = new AreaIdentifier(1);
        $event = new AreaJoinedEvent(
            new Area($areaId, new AreaName('battleon'), new RoomIdentifier(1))
        );

        $this->script->handle($event, $this->ctx);

        $this->assertTrue($this->script->isDone());
        $this->assertSame(ScriptResult::Success, $this->script->result());
        $this->assertSame($areaId, $this->ctx->get('area_id'));
    }

    #[Test]
    public function it_does_not_succeed_when_non_battleon_area_is_joined(): void
    {
        $event = new AreaJoinedEvent(
            new Area(new AreaIdentifier(2), new AreaName('othermap'), new RoomIdentifier(1))
        );

        $this->script->handle($event, $this->ctx);

        $this->assertFalse($this->script->isDone());
    }
}
```

- [ ] **Step 2: Run to verify failure**

```bash
./vendor/bin/phpunit --filter JoinBattleonScriptTest
```

- [ ] **Step 3: Implement `JoinBattleonScript`**

```php
// src/Scripts/JoinBattleonScript.php
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
```

- [ ] **Step 4: Run tests to verify they pass**

```bash
./vendor/bin/phpunit --filter JoinBattleonScriptTest
```

- [ ] **Step 5: Lint, analyze, format**

```bash
./vendor/bin/mago lint src/Scripts/JoinBattleonScript.php
./vendor/bin/mago analyze src/Scripts/JoinBattleonScript.php
./vendor/bin/mago format src/Scripts/JoinBattleonScript.php
```

- [ ] **Step 6: Commit**

```bash
git add src/Scripts/JoinBattleonScript.php tests/Unit/Scripts/JoinBattleonScriptTest.php
git commit -m "feat(scripts): add JoinBattleonScript (login sequence step 2)"
```

---

### Task 9: `LoadInventoryScript` (atomic step 3)

**Files:**
- Create: `src/Scripts/LoadInventoryScript.php`
- Create: `tests/Unit/Scripts/LoadInventoryScriptTest.php`

- [ ] **Step 1: Write the failing test**

```php
// tests/Unit/Scripts/LoadInventoryScriptTest.php
<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Scripts;

use AqwSocketClient\Commands\LoadPlayerInventoryCommand;
use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use AqwSocketClient\Scripts\ClientContext;
use AqwSocketClient\Scripts\LoadInventoryScript;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoadInventoryScriptTest extends TestCase
{
    private ClientContext $ctx;

    protected function setUp(): void
    {
        $this->ctx = new ClientContext();
        $this->ctx->set('socket_id', new SocketIdentifier(1));
        $this->ctx->set('area_id', new AreaIdentifier(1));
    }

    #[Test]
    public function it_sends_load_inventory_command_on_start(): void
    {
        $script = new LoadInventoryScript();
        $command = $script->start($this->ctx);
        $this->assertInstanceOf(LoadPlayerInventoryCommand::class, $command);
    }

    #[Test]
    public function it_succeeds_when_inventory_loaded_event_arrives(): void
    {
        $script = new LoadInventoryScript();
        $script->handle(new PlayerInventoryLoadedEvent(), $this->ctx);

        $this->assertTrue($script->isDone());
        $this->assertSame(ScriptResult::Success, $script->result());
    }
}
```

- [ ] **Step 2: Run to verify failure**

```bash
./vendor/bin/phpunit --filter LoadInventoryScriptTest
```

- [ ] **Step 3: Implement `LoadInventoryScript`**

```php
// src/Scripts/LoadInventoryScript.php
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
```

- [ ] **Step 4: Run tests to verify they pass**

```bash
./vendor/bin/phpunit --filter LoadInventoryScriptTest
```

- [ ] **Step 5: Lint, analyze, format**

```bash
./vendor/bin/mago lint src/Scripts/LoadInventoryScript.php
./vendor/bin/mago analyze src/Scripts/LoadInventoryScript.php
./vendor/bin/mago format src/Scripts/LoadInventoryScript.php
```

- [ ] **Step 6: Commit**

```bash
git add src/Scripts/LoadInventoryScript.php tests/Unit/Scripts/LoadInventoryScriptTest.php
git commit -m "feat(scripts): add LoadInventoryScript (login sequence step 3)"
```

---

### Task 10: Rewrite `LoginScript` as `SequenceScript` facade

**Files:**
- Modify: `src/Scripts/LoginScript.php`

`LoginScript` becomes a thin `SequenceScript` wrapping the three atomic scripts. It keeps its public interface (`$socketId`, `$areaId`) but reads from context after completion.

> Note: `LoginScript` currently exposes `$socketId` and `$areaId` as public properties. Under the new design, callers should read context instead. Keep them as legacy accessors for backward compatibility, but they are now populated by reading from `ClientContext` after the sequence ends. Since the client doesn't expose context externally yet, consider dropping these properties in a future clean-up. For now, **remove them** — callers using `run()` typically check `isDone()` and `result()` only.

- [ ] **Step 1: Rewrite `LoginScript`**

```php
// src/Scripts/LoginScript.php
<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use AqwSocketClient\Objects\Names\PlayerName;

/**
 * Orchestrates the full login sequence:
 *   1. Establish connection and authenticate.
 *   2. Join the battleon area.
 *   3. Load the player inventory.
 *
 * Equivalent to:
 *   new SequenceScript([
 *       new ConnectAndLoginScript($playerName, $token),
 *       new JoinBattleonScript(),
 *       new LoadInventoryScript(),
 *   ])
 */
final class LoginScript extends SequenceScript
{
    public function __construct(
        PlayerName $playerName,
        #[\SensitiveParameter]
        string $token,
    ) {
        parent::__construct([
            new ConnectAndLoginScript($playerName, $token),
            new JoinBattleonScript(),
            new LoadInventoryScript(),
        ]);
    }
}
```

- [ ] **Step 2: Lint and analyze**

```bash
./vendor/bin/mago lint src/Scripts/LoginScript.php
./vendor/bin/mago analyze src/Scripts/LoginScript.php
```

- [ ] **Step 3: Commit**

```bash
git add src/Scripts/LoginScript.php
git commit -m "refactor(scripts): rewrite LoginScript as SequenceScript of atomic steps"
```

---

### Task 11: Update `LoginScriptTest`

The existing `LoginScriptTest` tests the old monolithic `LoginScript` directly. Under the new design, `LoginScript` is a `SequenceScript` — its behavior is tested via integration. Rewrite the test to verify the full sequence integration.

**Files:**
- Modify: `tests/Unit/Scripts/LoginScriptTest.php`

- [ ] **Step 1: Rewrite `LoginScriptTest`**

```php
// tests/Unit/Scripts/LoginScriptTest.php
<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Scripts;

use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Objects\Area\Area;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\RoomIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use AqwSocketClient\Objects\Names\AreaName;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Scripts\ClientContext;
use AqwSocketClient\Scripts\LoginScript;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoginScriptTest extends TestCase
{
    private LoginScript $script;
    private ClientContext $ctx;

    protected function setUp(): void
    {
        $this->ctx = new ClientContext();
        $this->script = new LoginScript(new PlayerName('Hilise'), 'token');
    }

    private function runFullSequence(): void
    {
        $socketId = new SocketIdentifier(1);
        $areaId = new AreaIdentifier(1);

        $this->script->handle(new ConnectionEstablishedEvent(), $this->ctx);
        $this->script->handle(new LoginRespondedEvent(true, $socketId), $this->ctx);
        $this->script->handle(new AreaJoinedEvent(
            new Area($areaId, new AreaName('battleon'), new RoomIdentifier(1))
        ), $this->ctx);
        $this->script->handle(new PlayerInventoryLoadedEvent(), $this->ctx);
    }

    #[Test]
    public function it_succeeds_after_full_login_sequence(): void
    {
        $this->runFullSequence();

        $this->assertTrue($this->script->isDone());
        $this->assertSame(ScriptResult::Success, $this->script->result());
    }

    #[Test]
    public function it_fails_when_login_responded_not_success(): void
    {
        $this->script->handle(new ConnectionEstablishedEvent(), $this->ctx);
        $this->script->handle(new LoginRespondedEvent(false, null), $this->ctx);

        $this->assertTrue($this->script->isDone());
        $this->assertSame(ScriptResult::Failed, $this->script->result());
    }

    #[Test]
    public function it_stores_socket_id_in_context(): void
    {
        $socketId = new SocketIdentifier(42);
        $this->script->handle(new ConnectionEstablishedEvent(), $this->ctx);
        $this->script->handle(new LoginRespondedEvent(true, $socketId), $this->ctx);

        $this->assertSame($socketId, $this->ctx->get('socket_id'));
    }

    #[Test]
    public function it_stores_area_id_in_context(): void
    {
        $areaId = new AreaIdentifier(7);
        $socketId = new SocketIdentifier(1);

        $this->script->handle(new ConnectionEstablishedEvent(), $this->ctx);
        $this->script->handle(new LoginRespondedEvent(true, $socketId), $this->ctx);
        $this->script->handle(new AreaJoinedEvent(
            new Area($areaId, new AreaName('battleon'), new RoomIdentifier(1))
        ), $this->ctx);

        $this->assertSame($areaId, $this->ctx->get('area_id'));
    }
}
```

- [ ] **Step 2: Run the test**

```bash
./vendor/bin/phpunit --filter LoginScriptTest
```

Expected: all PASS.

- [ ] **Step 3: Commit**

```bash
git add tests/Unit/Scripts/LoginScriptTest.php
git commit -m "test(scripts): update LoginScriptTest for SequenceScript-based LoginScript"
```

---

### Task 12: Update `SocketClientTest` and run full suite

`SocketClientTest::it_can_run_a_script()` uses `LoginScript` and asserts specific packets were sent. The new design sends the same commands but `LoadPlayerInventoryCommand` is now sent after `PlayerInventoryLoadedEvent` is received (not before). The test needs a `loadInventory()` fixture queued so that `LoadInventoryScript` can respond to `PlayerInventoryLoadedEvent`.

The test currently queues `loadInventory()` already — but the old `LoginScript` marked success before receiving that message. Now the sequence only ends after `PlayerInventoryLoadedEvent` arrives.

**Files:**
- Modify: `tests/Unit/Clients/SocketClientTest.php`

- [ ] **Step 1: Update tests in `SocketClientTest` that use the old `handle()` signature**

Two tests call `$script->handle($event)` with the old single-argument signature. They need a `ClientContext` argument added:

**`it_results_in_disconnect_if_socket_closes`** — update the `handle()` call:

```php
// Before:
$script->handle(new AreaJoinedEvent(...));

// After (add a ClientContext):
$script->handle(new AreaJoinedEvent(
    new Area($areaId, new AreaName('battleon'), new RoomIdentifier(1)),
), new ClientContext());
```

Add `use AqwSocketClient\Scripts\ClientContext;` to imports.

**`it_can_run_a_script`** — this test does not call `handle()` directly, it uses `$client->run($script)`. The assertions remain unchanged (same three commands expected, same `isDone()` / `result()` check). The response queue order already matches the new flow:

1. `domainPolicy()` → `ConnectionEstablishedEvent` → `LoginCommand` sent
2. `loginReponded()` → `LoginRespondedEvent` → advance, `JoinInitialAreaCommand` sent from `JoinBattleonScript::start()`
3. `moveToArea('battleon')` → `AreaJoinedEvent` → advance, `LoadPlayerInventoryCommand` sent from `LoadInventoryScript::start()`
4. `loadInventory()` → `PlayerInventoryLoadedEvent` → `success()`

No changes needed to this test beyond ensuring `LoginScript` is properly wired (which Task 10 handles).

Also remove the `$script->socketId` and `$script->areaId` property accesses if any test references them — these properties no longer exist.

- [ ] **Step 2: Run just this test**

```bash
./vendor/bin/phpunit --filter SocketClientTest
```

Expected: all PASS.

- [ ] **Step 3: Run the full quality suite**

```bash
composer quality
```

Expected: lint OK, analyze OK, fmt OK, all tests PASS.

- [ ] **Step 4: Fix any issues found by `composer quality`**

If mago flags issues, fix them and re-run.

- [ ] **Step 5: Final commit**

```bash
git add tests/Unit/Clients/SocketClientTest.php
git commit -m "test(clients): update SocketClientTest for new AbstractClient loop"
```

---

## Summary of new files and modified files

**Created:**
- `src/Scripts/ClientContext.php`
- `src/Scripts/SequenceScript.php`
- `src/Scripts/Pipeline.php`
- `src/Scripts/ConnectAndLoginScript.php`
- `src/Scripts/JoinBattleonScript.php`
- `src/Scripts/LoadInventoryScript.php`
- `tests/Unit/Scripts/ClientContextTest.php`
- `tests/Unit/Scripts/SequenceScriptTest.php`
- `tests/Unit/Scripts/PipelineTest.php`
- `tests/Unit/Scripts/ConnectAndLoginScriptTest.php`
- `tests/Unit/Scripts/JoinBattleonScriptTest.php`
- `tests/Unit/Scripts/LoadInventoryScriptTest.php`

**Modified:**
- `src/Interfaces/ScriptInterface.php` — add `start()`, narrow `handle()` return
- `src/Scripts/AbstractScript.php` — add default `start()`
- `src/Scripts/LoginScript.php` — rewrite as `SequenceScript` facade
- `src/Clients/AbstractClient.php` — add command queue + `ClientContext`
- `tests/Unit/Scripts/LoginScriptTest.php` — rewrite for new design
- `tests/Unit/Clients/SocketClientTest.php` — update for new loop
