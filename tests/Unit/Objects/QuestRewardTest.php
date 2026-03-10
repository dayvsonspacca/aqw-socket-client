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
        $reward = new QuestReward(new ItemIdentifier(1), 2.5, 1);

        $this->assertInstanceOf(QuestReward::class, $reward);
        $this->assertSame($reward->itemIdentifier->value, 1);
        $this->assertSame($reward->rate, 2.5);
        $this->assertSame($reward->quantity, 1);
    }

    #[Test]
    public function it_can_create_with_rate_100(): void
    {
        $reward = new QuestReward(new ItemIdentifier(1), 100.0, 1);

        $this->assertSame($reward->rate, 100.0);
    }

    #[Test]
    public function should_throw_exception_when_rate_zero(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new QuestReward(new ItemIdentifier(1), 0.0, 1);
    }

    #[Test]
    public function should_throw_exception_when_rate_above_100(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new QuestReward(new ItemIdentifier(1), 100.1, 1);
    }

    #[Test]
    public function should_throw_exception_when_quantity_zero(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new QuestReward(new ItemIdentifier(1), 50.0, 0);
    }
}
