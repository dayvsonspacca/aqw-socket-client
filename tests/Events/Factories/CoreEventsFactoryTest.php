<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Events\Factories;

use AqwSocketClient\Events\{ConnectionEstabilishedEvent, LoginSuccessfulEvent, RawMessageEvent};
use AqwSocketClient\Events\Factories\CoreEventsFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CoreEventsFactoryTest extends TestCase
{
    #[Test]
    public function from_message_always_returns_raw_message_event(): void
    {
        $factory = new CoreEventsFactory();
        $message = 'any message';

        $events = $factory->fromMessage($message);

        $this->assertContainsOnlyInstancesOf(RawMessageEvent::class, $events);
        $this->assertSame($message, $events[0]->message);
    }

    #[Test]
    public function from_message_detects_connection_established_event(): void
    {
        $factory = new CoreEventsFactory();
        $message = '<cross-domain-policy>';

        $events = $factory->fromMessage($message);

        $this->assertContainsOnlyInstancesOf(RawMessageEvent::class, [$events[0]]);
        $this->assertInstanceOf(ConnectionEstabilishedEvent::class, $events[1]);
    }

    #[Test]
    public function from_message_detects_login_successful_event(): void
    {
        $factory = new CoreEventsFactory();
        $message = '%xt%loginResponse%-1%true%';

        $events = $factory->fromMessage($message);

        $this->assertInstanceOf(RawMessageEvent::class, $events[0]);
        $this->assertInstanceOf(LoginSuccessfulEvent::class, $events[1]);
    }
}
