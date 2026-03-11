<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Quest\GoldReward;
use AqwSocketClient\Objects\Quest\QuestRewardInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GoldRewardTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $reward = new GoldReward(13_000);

        $this->assertInstanceOf(GoldReward::class, $reward);
        $this->assertInstanceOf(QuestRewardInterface::class, $reward);
        $this->assertSame(13_000, $reward->amount);
    }

    #[Test]
    public function it_can_create_with_zero(): void
    {
        $reward = new GoldReward(0);

        $this->assertSame(0, $reward->amount);
    }

    #[Test]
    public function should_throw_exception_when_amount_negative(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new GoldReward(-1);
    }
}
