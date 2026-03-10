<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Identifiers\ItemIdentifier;
use AqwSocketClient\Objects\QuestTurnInItem;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class QuestTurnInItemTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $item = new QuestTurnInItem(new ItemIdentifier(1), 5);

        $this->assertInstanceOf(QuestTurnInItem::class, $item);
        $this->assertSame($item->itemIdentifier->value, 1);
        $this->assertSame($item->quantity, 5);
    }

    #[Test]
    public function should_throw_exception_when_quantity_zero(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new QuestTurnInItem(new ItemIdentifier(1), 0);
    }

    #[Test]
    public function should_throw_exception_when_quantity_negative(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new QuestTurnInItem(new ItemIdentifier(1), -1);
    }
}
