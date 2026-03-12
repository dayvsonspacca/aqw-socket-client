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

    /** @var Closure|null */
    private ?Closure $successCallback = null;

    /** @var list<class-string<EventInterface>> */
    private array $failureClasses = [];

    private function __construct(
        private readonly CommandInterface $initialCommand,
    ) {}

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
            /** @var CommandInterface|null */
            $command = null;

            if ($this->successCallback !== null) {
                /** @var CommandInterface|null */
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
