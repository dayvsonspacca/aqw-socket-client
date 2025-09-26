<?php

namespace AqwSocketClient\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use AqwSocketClient\Factories\CoreEventsFactory;
use AqwSocketClient\Events\RawMessageEvent;
use AqwSocketClient\Events\ConnectionEstabilishedEvent;
use AqwSocketClient\Events\LoginSuccessfulEvent;

class CoreEventsFactoryTest extends TestCase
{
    #[Test]
    public function from_message_always_returns_raw_message_event(): void
    {
        $factory = new CoreEventsFactory();
        $message = "any message";

        $events = $factory->fromMessage($message);

        $this->assertContainsOnlyInstancesOf(RawMessageEvent::class, $events);
        $this->assertSame($message, $events[0]->message);
    }

    #[Test]
    public function from_message_detects_connection_established_event(): void
    {
        $factory = new CoreEventsFactory();
        $message = "<cross-domain-policy>";

        $events = $factory->fromMessage($message);

        $this->assertContainsOnlyInstancesOf(RawMessageEvent::class, [$events[0]]);
        $this->assertInstanceOf(ConnectionEstabilishedEvent::class, $events[1]);
    }

    #[Test]
    public function from_message_detects_login_successful_event(): void
    {
        $factory = new CoreEventsFactory();
        $message = "%xt%loginResponse%-1%true%";

        $events = $factory->fromMessage($message);

        $this->assertInstanceOf(RawMessageEvent::class, $events[0]);
        $this->assertInstanceOf(LoginSuccessfulEvent::class, $events[1]);
    }
}
