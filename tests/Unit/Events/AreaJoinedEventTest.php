<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\AreaIdentifier;
use AqwSocketClient\Tests\Helpers\MessageGenerator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AreaJoinedEventTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $message = JsonMessage::from(MessageGenerator::moveToArea('battleon', new AreaIdentifier(2)));

        /** @var JsonMessage $message */
        $event = AreaJoinedEvent::from($message);

        $this->assertInstanceOf(AreaJoinedEvent::class, $event);
        $this->assertSame($event->mapName, 'battleon');
        $this->assertSame($event->areaId->value, 2);
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
