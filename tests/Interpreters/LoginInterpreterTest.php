<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests;

use AqwSocketClient\Events\ConnectionEstabilishedEvent;
use AqwSocketClient\Interpreters\LoginInterpreter;
use AqwSocketClient\Messages\XmlMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LoginInterpreterTest extends TestCase
{
    private readonly LoginInterpreter $interpreter;

    protected function setUp(): void
    {
        $this->interpreter = new LoginInterpreter();
    }

    #[Test]
    public function should_interpreter_connection_estabilished(): void
    {
        $message   = XmlMessage::fromString("<cross-domain-policy><allow-access-from domain='*' to-ports='5591' /></cross-domain-policy>");

        $events = $this->interpreter->interpret($message);

        $this->assertIsArray($events);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(ConnectionEstabilishedEvent::class, $events[0]);
    }
}