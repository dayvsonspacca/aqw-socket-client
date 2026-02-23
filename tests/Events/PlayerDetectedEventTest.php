<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Commands;

use AqwSocketClient\Events\PlayerDetectedEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PlayerDetectedEventTest extends TestCase
{
    private readonly PlayerDetectedEvent $event;

    protected function setUp(): void
    {
        $this->event = new PlayerDetectedEvent('PlayerOne');
    }

    #[Test]
    public function should_create_player_detected_event(): void
    {
        $this->assertInstanceOf(PlayerDetectedEvent::class, $this->event);
        $this->assertSame('PlayerOne', $this->event->name);
    }
}
