<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests;

use AqwSocketClient\Commands\{AfterLoginCommand, LoginCommand};
use AqwSocketClient\Events\{ConnectionEstabilishedEvent, LoginSuccessfulEvent, RawMessageEvent};
use AqwSocketClient\Factories\CoreEventsHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CoreEventsHandlerTest extends TestCase
{
    #[Test]
    public function handle_connection_established_generates_login_command(): void
    {
        $handler = new CoreEventsHandler('PlayerOne', 'Token123');
        $events  = [new ConnectionEstabilishedEvent()];

        $commands = $handler->handle($events);

        $this->assertCount(1, $commands);
        $this->assertInstanceOf(LoginCommand::class, $commands[0]);
    }

    #[Test]
    public function handle_login_successful_generates_after_login_command(): void
    {
        $handler = new CoreEventsHandler('PlayerOne', 'Token123');
        $events  = [new LoginSuccessfulEvent()];

        $commands = $handler->handle($events);

        $this->assertCount(1, $commands);
        $this->assertInstanceOf(AfterLoginCommand::class, $commands[0]);
    }

    #[Test]
    public function handle_raw_message_generates_no_command(): void
    {
        $handler = new CoreEventsHandler('PlayerOne', 'Token123');
        $events  = [new RawMessageEvent('any message')];

        $commands = $handler->handle($events);

        $this->assertCount(0, $commands);
    }

    #[Test]
    public function handle_multiple_events_generates_correct_commands(): void
    {
        $handler = new CoreEventsHandler('PlayerOne', 'Token123');
        $events  = [
            new ConnectionEstabilishedEvent(),
            new RawMessageEvent('message'),
            new LoginSuccessfulEvent()
        ];

        $commands = $handler->handle($events);

        $this->assertCount(2, $commands);
        $this->assertInstanceOf(LoginCommand::class, $commands[0]);
        $this->assertInstanceOf(AfterLoginCommand::class, $commands[1]);
    }
}
