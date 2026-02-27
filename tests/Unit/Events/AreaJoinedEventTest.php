<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Messages\JsonMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AreaJoinedEventTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $message = JsonMessage::from(
            '{"t":"xt","b":{"r":-1,"o":{"cmd":"moveToArea","areaName":"battleon-1","uoBranch":[],"strMapFileName":"Battleon/town-Battleon-7Nov25r1.swf","intType":"2","monBranch":[],"mondef":[],"areaId":3,"strMapName":"battleon"}}}',
        );

        /** @var JsonMessage $message */
        $event = AreaJoinedEvent::from($message);

        $this->assertInstanceOf(AreaJoinedEvent::class, $event);
        $this->assertSame($event->mapName, 'battleon');
    }

    #[Test]
    public function it_creates_null_on_invalid_messages(): void
    {
        $message = JsonMessage::from(
            '{"t":"xt","b":{"r":-1,"o":{"cmd":"equipItem","areaName":"battleon-1","uoBranch":[],"strMapFileName":"Battleon/town-Battleon-7Nov25r1.swf","intType":"2","monBranch":[],"sExtra":"","areaId":3,"strMapName":"battleon"}}}',
        );

        /** @var JsonMessage $message */
        $event = AreaJoinedEvent::from($message);

        $this->assertNull($event);
    }
}
