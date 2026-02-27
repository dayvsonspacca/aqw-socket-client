<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Messages\JsonMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PlayerInventoryLoadedEventTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $message = JsonMessage::from(
            '{"t":"xt","b":{"r":-1,"o":{"bankCount":57,"cmd":"loadInventoryBig","items":[]}}}',
        );

        /** @var JsonMessage $message */
        $event = PlayerInventoryLoadedEvent::from($message);

        $this->assertInstanceOf(PlayerInventoryLoadedEvent::class, $event);
    }

    #[Test]
    public function it_creates_null_on_invalid_messages(): void
    {
        $message = JsonMessage::from(
            '{"t":"xt","b":{"r":-1,"o":{"cmd":"equipItem","areaName":"battleon-1","uoBranch":[],"strMapFileName":"Battleon/town-Battleon-7Nov25r1.swf","intType":"2","monBranch":[],"sExtra":"","areaId":3,"strMapName":"battleon"}}}',
        );

        /** @var JsonMessage $message */
        $event = PlayerInventoryLoadedEvent::from($message);

        $this->assertNull($event);
    }
}
