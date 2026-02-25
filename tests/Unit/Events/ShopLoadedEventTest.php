<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\ShopLoadedEvent;
use AqwSocketClient\Objects\Shop;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ShopLoadedEventTest extends TestCase
{
    private ShopLoadedEvent $event;

    protected function setUp(): void
    {
        $shop = $this->createMock(Shop::class);
        $this->event = new ShopLoadedEvent($shop);
    }

    #[Test]
    public function should_create_shop_loaded_event(): void
    {
        $this->assertInstanceOf(ShopLoadedEvent::class, $this->event);
        $this->assertInstanceOf(Shop::class, $this->event->shop);
    }
}
