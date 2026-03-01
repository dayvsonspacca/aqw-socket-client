<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\PlayerDetectedEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\Names\PlayerName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PlayerDetectedEventTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $message = DelimitedMessage::from(MessageGenerator::moveTowards(new PlayerName('made2903')));

        /** @var DelimitedMessage $message */
        $event = PlayerDetectedEvent::from($message);
        $this->assertSame('made2903', $event->name);
        $this->assertInstanceOf(PlayerDetectedEvent::class, $event);

        $message = DelimitedMessage::from(MessageGenerator::exitArea(new PlayerName('Hilise')));

        /** @var DelimitedMessage $message */
        $event = PlayerDetectedEvent::from($message);

        $this->assertInstanceOf(PlayerDetectedEvent::class, $event);
        $this->assertSame('Hilise', $event->name);
    }

    #[Test]
    public function it_creates_null_on_invalid_messages(): void
    {
        $message = JsonMessage::from(MessageGenerator::loadInventory());

        /** @var JsonMessage $message */
        $event = PlayerDetectedEvent::from($message);

        $this->assertNull($event);
    }
}
