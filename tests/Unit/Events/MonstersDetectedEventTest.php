<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Events;

use AqwSocketClient\Events\MonstersDetectedEvent;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Tests\Helpers\MessageGenerator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MonstersDetectedEventTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $message = JsonMessage::from(MessageGenerator::monstersDetected());

        /** @var JsonMessage $message */

        $event = MonstersDetectedEvent::from($message);
        $this->assertInstanceOf(MonstersDetectedEvent::class, $event);
    }

    #[Test]
    public function it_creates_null_on_invalid_messages(): void
    {
        $message = JsonMessage::from(MessageGenerator::loadInventory());

        /** @var JsonMessage $message */
        $event = MonstersDetectedEvent::from($message);

        $this->assertNull($event);
    }
}
