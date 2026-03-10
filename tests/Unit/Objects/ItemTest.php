<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Enums\EquipSlot;
use AqwSocketClient\Enums\ItemType;
use AqwSocketClient\Enums\Rarity;
use AqwSocketClient\Enums\Tag;
use AqwSocketClient\Objects\GameFileMetadata;
use AqwSocketClient\Objects\Identifiers\ItemIdentifier;
use AqwSocketClient\Objects\Item;
use AqwSocketClient\Objects\Names\ItemName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
{
    #[Test]
    public function it_can_create_equippable_item(): void
    {
        $item = new Item(
            new ItemIdentifier(5528),
            new ItemName('Voidfangs of Nulgath'),
            ItemType::Dagger,
            EquipSlot::Weapon,
            Rarity::Weird,
            1,
            new GameFileMetadata('MiltonPoolsword06', 'items/swords/MiltonPoolsword06.swf'),
            [],
        );

        $this->assertInstanceOf(Item::class, $item);
        $this->assertSame($item->identifier->value, 5528);
        $this->assertSame($item->name->value, 'Voidfangs of Nulgath');
        $this->assertSame($item->type, ItemType::Dagger);
        $this->assertSame($item->equipSlot, EquipSlot::Weapon);
        $this->assertSame($item->rarity, Rarity::Weird);
        $this->assertSame($item->maxStack, 1);
        $this->assertNotNull($item->file);
        $this->assertEmpty($item->tags);
    }

    #[Test]
    public function it_can_create_resource_item(): void
    {
        $item = new Item(
            new ItemIdentifier(6136),
            new ItemName('Gem of Nulgath'),
            ItemType::Resource,
            EquipSlot::None,
            null,
            1000,
            null,
            [Tag::QuestItem, Tag::AcPurchasable],
        );

        $this->assertNull($item->rarity);
        $this->assertNull($item->file);
        $this->assertContains(Tag::QuestItem, $item->tags);
        $this->assertContains(Tag::AcPurchasable, $item->tags);
    }

    #[Test]
    public function it_can_create_member_only_item(): void
    {
        $item = new Item(
            new ItemIdentifier(4809),
            new ItemName('Oblivion Blade of Nulgath Pet (Rare)'),
            ItemType::Pet,
            EquipSlot::Pet,
            Rarity::Rare,
            1,
            new GameFileMetadata('MiltonPoolOblivionBladeRarer1', 'items/pets/MiltonPoolOblivionBladeRarer1.swf'),
            [Tag::MemberOnly],
        );

        $this->assertSame($item->rarity, Rarity::Rare);
        $this->assertContains(Tag::MemberOnly, $item->tags);
    }

    #[Test]
    public function should_throw_exception_when_max_stack_zero(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new Item(
            new ItemIdentifier(1),
            new ItemName('Item'),
            ItemType::Item,
            EquipSlot::None,
            null,
            0,
            null,
            [],
        );
    }
}
