<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Events;

use AqwSocketClient\Events\PlayerLoggedOutEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PlayerLoggedOutEventTest extends TestCase
{
    #[Test]
    public function should_create_player_logged_out_event(): void
    {
        $event = new PlayerLoggedOutEvent();

        $this->assertInstanceOf(PlayerLoggedOutEvent::class, $event);
    }
}
