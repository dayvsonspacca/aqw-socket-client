<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Events;

use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Names\AreaName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AreaJoinedEventTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $areaName = new AreaName('battleon');
        $areaIdentifier = new AreaIdentifier(2);

        $message = JsonMessage::from(MessageGenerator::moveToArea($areaName, $areaIdentifier));

        /** @var JsonMessage $message */
        $event = AreaJoinedEvent::from($message);

        $this->assertInstanceOf(AreaJoinedEvent::class, $event);
        $this->assertEquals($areaName, $event->area->name);
        $this->assertEquals($areaIdentifier, $event->area->identifier);
    }

    #[Test]
    public function it_creates_null_on_invalid_messages(): void
    {
        $message = JsonMessage::from(MessageGenerator::loadInventory());

        /** @var JsonMessage $message */
        $event = AreaJoinedEvent::from($message);

        $this->assertNull($event);
    }
}
