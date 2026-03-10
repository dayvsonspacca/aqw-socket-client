<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Identifiers\ItemIdentifier;
use AqwSocketClient\Objects\ItemReward;
use AqwSocketClient\Objects\QuestRewardInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ItemRewardTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $reward = new ItemReward(new ItemIdentifier(1), 10, 1);

        $this->assertInstanceOf(ItemReward::class, $reward);
        $this->assertInstanceOf(QuestRewardInterface::class, $reward);
        $this->assertSame(1, $reward->itemIdentifier->value);
        $this->assertSame(10, $reward->rate);
        $this->assertSame(1, $reward->quantity);
    }

    #[Test]
    public function it_can_create_with_rate_1(): void
    {
        $reward = new ItemReward(new ItemIdentifier(1), 1, 1);

        $this->assertSame(1, $reward->rate);
    }

    #[Test]
    public function it_can_create_with_rate_100(): void
    {
        $reward = new ItemReward(new ItemIdentifier(1), 100, 1);

        $this->assertSame(100, $reward->rate);
    }

    #[Test]
    public function should_throw_exception_when_rate_zero(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new ItemReward(new ItemIdentifier(1), 0, 1);
    }

    #[Test]
    public function should_throw_exception_when_rate_above_100(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new ItemReward(new ItemIdentifier(1), 101, 1);
    }

    #[Test]
    public function should_throw_exception_when_quantity_zero(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new ItemReward(new ItemIdentifier(1), 10, 0);
    }
}
