<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Events;

use AqwSocketClient\Enums\EquipmentSlot;
use AqwSocketClient\Events\PlayerPreferencesLoadedEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\Identifiers\ItemIdentifier;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PlayerPreferencesLoadedEventTest extends TestCase
{
    #[Test]
    public function it_creates_event_on_correct_message(): void
    {
        $message = JsonMessage::from(MessageGenerator::playerPreferencesLoaded());

        /** @var JsonMessage $message */
        $event = PlayerPreferencesLoadedEvent::from($message);

        $this->assertInstanceOf(PlayerPreferencesLoadedEvent::class, $event);
        $this->assertCount(4, $event->costumes);
        $this->assertArrayHasKey(EquipmentSlot::Costume->value, $event->costumes);
        $this->assertArrayHasKey(EquipmentSlot::Cape->value, $event->costumes);
        $this->assertArrayHasKey(EquipmentSlot::Weapon->value, $event->costumes);
        $this->assertArrayHasKey(EquipmentSlot::Helm->value, $event->costumes);
    }

    #[Test]
    public function it_maps_item_identifiers_correctly(): void
    {
        $message = JsonMessage::from(MessageGenerator::playerPreferencesLoaded());

        /** @var JsonMessage $message */
        $event = PlayerPreferencesLoadedEvent::from($message);

        $this->assertInstanceOf(PlayerPreferencesLoadedEvent::class, $event);
        $this->assertInstanceOf(ItemIdentifier::class, $event->costumes[EquipmentSlot::Costume->value]);
        $this->assertSame(83_965, $event->costumes[EquipmentSlot::Costume->value]->value);
        $this->assertSame(59_470, $event->costumes[EquipmentSlot::Cape->value]->value);
        $this->assertSame(59_471, $event->costumes[EquipmentSlot::Weapon->value]->value);
        $this->assertSame(74_726, $event->costumes[EquipmentSlot::Helm->value]->value);
    }

    #[Test]
    public function it_skips_unknown_slots(): void
    {
        $raw = '{"t":"xt","b":{"r":-1,"o":{"cmd":"loadPrefs","result":{"costumes":{"co":1,"unknown":9999},"loadouts":{},"prefs":{}},"success":true}}}';
        $message = JsonMessage::from($raw);

        /** @var JsonMessage $message */
        $event = PlayerPreferencesLoadedEvent::from($message);

        $this->assertInstanceOf(PlayerPreferencesLoadedEvent::class, $event);
        $this->assertCount(1, $event->costumes);
        $this->assertArrayNotHasKey('unknown', $event->costumes);
    }

    #[Test]
    public function it_returns_empty_costumes_when_absent(): void
    {
        $raw = '{"t":"xt","b":{"r":-1,"o":{"cmd":"loadPrefs","result":{"loadouts":{},"prefs":{}},"success":true}}}';
        $message = JsonMessage::from($raw);

        /** @var JsonMessage $message */
        $event = PlayerPreferencesLoadedEvent::from($message);

        $this->assertInstanceOf(PlayerPreferencesLoadedEvent::class, $event);
        $this->assertEmpty($event->costumes);
    }

    #[Test]
    public function it_returns_null_on_wrong_message_type(): void
    {
        $message = JsonMessage::from(MessageGenerator::loadInventory());

        /** @var JsonMessage $message */
        $event = PlayerPreferencesLoadedEvent::from($message);

        $this->assertNull($event);
    }
}
