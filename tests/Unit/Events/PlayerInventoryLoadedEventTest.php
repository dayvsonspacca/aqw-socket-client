<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PlayerInventoryLoadedEventTest extends TestCase
{
    private PlayerInventoryLoadedEvent $event;

    protected function setUp(): void
    {
        $this->event = new PlayerInventoryLoadedEvent([
            ['name' => 'Iron Sword', 'description' => 'a sword', 'type' => 'weapon', 'file_name' => 'sword.swf'],
            ['name' => 'Health Potion', 'description' => 'a sword', 'type' => 'weapon', 'file_name' => 'sword.swf'],
        ]);
    }

    #[Test]
    public function should_create_player_inventory_loaded_event(): void
    {
        $this->assertInstanceOf(PlayerInventoryLoadedEvent::class, $this->event);
        $this->assertCount(2, $this->event->items);
        $this->assertSame('Iron Sword', $this->event->items[0]['name']);
        $this->assertSame('Health Potion', $this->event->items[1]['name']);
    }
}
