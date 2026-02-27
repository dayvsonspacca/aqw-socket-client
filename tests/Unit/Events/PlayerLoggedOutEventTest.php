<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Events;

use AqwSocketClient\Events\PlayerLoggedOutEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Messages\XmlMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PlayerLoggedOutEventTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $message = XmlMessage::from(MessageGenerator::logout());

        /** @var XmlMessage $message */
        $event = PlayerLoggedOutEvent::from($message);

        $this->assertInstanceOf(PlayerLoggedOutEvent::class, $event);
    }

    #[Test]
    public function it_creates_null_on_invalid_messages(): void
    {
        $message = JsonMessage::from(MessageGenerator::loadInventory());

        /** @var JsonMessage $message */
        $event = PlayerLoggedOutEvent::from($message);

        $this->assertNull($event);
    }
}
