<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Events;

use AqwSocketClient\Enums\EquipmentSlot;
use AqwSocketClient\Events\ItemUnequippedEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\Identifiers\ItemIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ItemUnequippedEventTest extends TestCase
{
    #[Test]
    public function it_creates_event_on_correct_message(): void
    {
        $socketId = new SocketIdentifier(51_487);
        $itemId = new ItemIdentifier(80_006);
        $message = JsonMessage::from(MessageGenerator::unequipItem($socketId, $itemId, EquipmentSlot::Pet));

        /** @var JsonMessage $message */
        $event = ItemUnequippedEvent::from($message);

        $this->assertInstanceOf(ItemUnequippedEvent::class, $event);
        $this->assertSame(51_487, $event->socketId->value);
        $this->assertSame(80_006, $event->itemId->value);
        $this->assertSame(EquipmentSlot::Pet, $event->slot);
        $this->assertTrue($event->unload);
    }

    #[Test]
    public function it_defaults_unload_to_false_when_absent(): void
    {
        $raw = '{"t":"xt","b":{"r":-1,"o":{"cmd":"unequipItem","uid":1,"ItemID":1,"strES":"ar"}}}';
        $message = JsonMessage::from($raw);

        /** @var JsonMessage $message */
        $event = ItemUnequippedEvent::from($message);

        $this->assertInstanceOf(ItemUnequippedEvent::class, $event);
        $this->assertFalse($event->unload);
    }

    #[Test]
    public function it_returns_null_on_unknown_slot(): void
    {
        $raw = '{"t":"xt","b":{"r":-1,"o":{"cmd":"unequipItem","uid":1,"ItemID":1,"strES":"unknown","bUnload":true}}}';
        $message = JsonMessage::from($raw);

        /** @var JsonMessage $message */
        $event = ItemUnequippedEvent::from($message);

        $this->assertNull($event);
    }

    #[Test]
    public function it_returns_null_on_wrong_message_type(): void
    {
        $message = JsonMessage::from(MessageGenerator::loadInventory());

        /** @var JsonMessage $message */
        $event = ItemUnequippedEvent::from($message);

        $this->assertNull($event);
    }
}
