<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Identifiers\ItemIdentifier;
use AqwSocketClient\Objects\QuestReward;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class QuestRewardTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $reward = new QuestReward(new ItemIdentifier(1), 50, 1, false);

        $this->assertInstanceOf(QuestReward::class, $reward);
        $this->assertSame($reward->itemIdentifier->value, 1);
        $this->assertSame($reward->rate, 50);
        $this->assertSame($reward->quantity, 1);
        $this->assertFalse($reward->guaranteed);
    }

    #[Test]
    public function should_throw_exception_when_rate_below_1(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new QuestReward(new ItemIdentifier(1), 0, 1, false);
    }

    #[Test]
    public function should_throw_exception_when_rate_above_100(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new QuestReward(new ItemIdentifier(1), 101, 1, false);
    }

    #[Test]
    public function should_throw_exception_when_quantity_zero(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new QuestReward(new ItemIdentifier(1), 50, 0, false);
    }
}
