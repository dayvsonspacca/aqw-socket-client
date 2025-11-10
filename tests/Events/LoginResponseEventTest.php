<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Commands;

use AqwSocketClient\Events\LoginResponseEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoginResponseEventTest extends TestCase
{
    private readonly LoginResponseEvent $event;

    protected function setUp(): void
    {
        $this->event = new LoginResponseEvent(true, 2);
    }

    #[Test]
    public function should_create_login_response_event()
    {
        $this->assertInstanceOf(LoginResponseEvent::class, $this->event);
        $this->assertSame($this->event->socketId, 2);
    }
}
