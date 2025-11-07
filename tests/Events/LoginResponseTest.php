<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Commands;

use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Events\LoginResponseEvent;
use AqwSocketClient\Packet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoginResponseTest extends TestCase
{
    private readonly LoginResponseEvent $event;

    protected function setUp(): void
    {
        $this->event = new LoginResponseEvent(true);
    }

    #[Test]
    public function should_create_login_response_event()
    {
        $this->assertInstanceOf(LoginResponseEvent::class, $this->event);
    }
}