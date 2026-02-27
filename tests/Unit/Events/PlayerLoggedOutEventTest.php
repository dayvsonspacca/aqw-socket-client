<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Events;

use AqwSocketClient\Events\PlayerLoggedOutEvent;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Messages\XmlMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PlayerLoggedOutEventTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $event = PlayerLoggedOutEvent::from(XmlMessage::fromString(
            "<msg t='sys'><body action='logout' r='0'></body></msg>",
        ));

        $this->assertInstanceOf(PlayerLoggedOutEvent::class, $event);
    }

    #[Test]
    public function it_creates_null_on_invalid_messages(): void
    {
        $event = PlayerLoggedOutEvent::from(JsonMessage::fromString(
            '{"t":"xt","b":{"r":-1,"o":{"bankCount":57,"cmd":"loadInventoryBig","items":[]}}}',
        ));

        $this->assertNull($event);
    }
}
