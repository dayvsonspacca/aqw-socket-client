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
    public function handles(): array
    {
        return [StubEvent::class];
    }

    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface
    {
        $this->success();
        return null;
    }
}

final class FailingScript extends AbstractScript
{
    public function handles(): array
    {
        return [StubEvent::class];
    }

    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface
    {
        $this->failed();
        return null;
    }
}

final class WritingScript extends AbstractScript
{
    public function handles(): array
    {
        return [StubEvent::class];
    }

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

    public function handles(): array
    {
        return [StubEvent::class];
    }

    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface
    {
        $this->sawValue = $context->has('written') && $context->get('written') === true;
        $this->success();
        return null;
    }
}

final class ExpirableStubScript extends ExpirableScript
{
    public function handles(): array
    {
        return [StubEvent::class];
    }

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
