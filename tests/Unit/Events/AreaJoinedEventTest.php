<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\AreaJoinedEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AreaJoinedEventTest extends TestCase
{
    private readonly AreaJoinedEvent $event;

    protected function setUp(): void
    {
        $this->event = new AreaJoinedEvent(
            'battleon',
            1,
            42,
            ['PlayerOne', 'PlayerTwo'],
            [[
                'name' => 'Goblin',
                'race' => 'Goblin',
                'asset_name' => 'goblin_asset',
                'level' => 5,
                'hp' => 100,
            ]],
        );
    }

    #[Test]
    public function should_create_area_joined_event(): void
    {
        $this->assertInstanceOf(AreaJoinedEvent::class, $this->event);
        $this->assertSame('battleon', $this->event->mapName);
        $this->assertSame(1, $this->event->mapNumber);
        $this->assertSame(42, $this->event->areaId);
        $this->assertSame(['PlayerOne', 'PlayerTwo'], $this->event->players);
        $this->assertSame(
            [[
                'name' => 'Goblin',
                'race' => 'Goblin',
                'asset_name' => 'goblin_asset',
                'level' => 5,
                'hp' => 100,
            ]],
            $this->event->monsters,
        );
    }
}
