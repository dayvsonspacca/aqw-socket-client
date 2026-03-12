<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Events;

use AqwSocketClient\Enums\EquipmentSlot;
use AqwSocketClient\Events\ItemEquippedEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\Identifiers\ItemIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ItemEquippedEventTest extends TestCase
{
    #[Test]
    public function it_creates_event_on_correct_message(): void
    {
        $socketId = new SocketIdentifier(42);
        $itemId = new ItemIdentifier(1001);
        $message = JsonMessage::from(MessageGenerator::equipItem($socketId, $itemId, EquipmentSlot::Armor));

        /** @var JsonMessage $message */
        $event = ItemEquippedEvent::from($message);

        $this->assertInstanceOf(ItemEquippedEvent::class, $event);
        $this->assertSame(42, $event->socketId->value);
        $this->assertSame(1001, $event->item->identifier->value);
        $this->assertSame(EquipmentSlot::Armor, $event->item->slot);
        $this->assertSame('items/equip/armor.swf', $event->item->metadata->file);
        $this->assertSame('http://game.aq.com/', $event->item->metadata->link);
        $this->assertSame('AutoAdd', $event->item->boost);
    }

    #[Test]
    public function it_handles_nullable_boost(): void
    {
        $socketId = new SocketIdentifier(42);
        $itemId = new ItemIdentifier(1001);
        $message = JsonMessage::from(MessageGenerator::equipItemWithoutBoost(
            $socketId,
            $itemId,
            EquipmentSlot::Weapon,
        ));

        /** @var JsonMessage $message */
        $event = ItemEquippedEvent::from($message);

        $this->assertInstanceOf(ItemEquippedEvent::class, $event);
        $this->assertNull($event->item->boost);
    }

    #[Test]
    public function it_returns_null_on_unknown_slot(): void
    {
        $raw = '{"t":"xt","b":{"r":-1,"o":{"cmd":"equipItem","uid":1,"ItemID":1,"strES":"unknown","sFile":"items/test.swf","sLink":"http://game.aq.com/"}}}';
        $message = JsonMessage::from($raw);

        /** @var JsonMessage $message */
        $event = ItemEquippedEvent::from($message);

        $this->assertNull($event);
    }

    #[Test]
    public function it_returns_null_on_wrong_message_type(): void
    {
        $message = JsonMessage::from(MessageGenerator::loadInventory());

        /** @var JsonMessage $message */
        $event = ItemEquippedEvent::from($message);

        $this->assertNull($event);
    }
}
