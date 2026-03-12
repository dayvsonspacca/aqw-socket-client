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
            ->waitFor(SuccessEvent::class, static fn(SuccessEvent $_e, ClientContext $_ctx): ?CommandInterface => $followUp);

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
            ->waitFor(SuccessEvent::class, static function (SuccessEvent $_e, ClientContext $ctx): ?CommandInterface {
                $ctx->set('key', 'value');
                return null;
            });

        $pipeline->handle(new SuccessEvent(), $this->ctx);

        $this->assertSame('value', $this->ctx->get('key'));
    }
}
