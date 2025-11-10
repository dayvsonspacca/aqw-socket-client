<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Listeners;

use AqwSocketClient\Events\{JoinedAreaEvent, LoginResponseEvent};
use AqwSocketClient\Listeners\GlobalPlayerListener;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GlobalPlayerListenerTest extends TestCase
{
    private GlobalPlayerListener $listener;

    protected function setUp(): void
    {
        $this->listener = new GlobalPlayerListener();
    }

    #[Test]
    public function should_listen_to_login_response_event_and_update_socket_id()
    {
        $event = new LoginResponseEvent(true, 5);

        $this->assertFalse(isset($this->listener->socketId));

        $this->listener->listen($event);

        $this->assertSame($this->listener->socketId, 5);
    }

    #[Test]
    public function should_listen_to_joined_area_event_and_update_area_id()
    {
        $event = new JoinedAreaEvent('yulgar', 2, 6, []);

        $this->assertFalse(isset($this->listener->areaId));

        $this->listener->listen($event);

        $this->assertSame($this->listener->areaId, 6);
    }
}
