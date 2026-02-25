<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\LoginRespondedEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoginRespondedEventTest extends TestCase
{
    private LoginRespondedEvent $event;

    protected function setUp(): void
    {
        $this->event = new LoginRespondedEvent(true, 2);
    }

    #[Test]
    public function should_create_login_responded_event(): void
    {
        $this->assertInstanceOf(LoginRespondedEvent::class, $this->event);
        $this->assertSame($this->event->socketId, 2);
    }
}
