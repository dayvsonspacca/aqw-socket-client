<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\ExperienceReward;
use AqwSocketClient\Objects\QuestRewardInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ExperienceRewardTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $reward = new ExperienceReward(300);

        $this->assertInstanceOf(ExperienceReward::class, $reward);
        $this->assertInstanceOf(QuestRewardInterface::class, $reward);
        $this->assertSame(300, $reward->amount);
    }

    #[Test]
    public function it_can_create_with_zero(): void
    {
        $reward = new ExperienceReward(0);

        $this->assertSame(0, $reward->amount);
    }

    #[Test]
    public function should_throw_exception_when_amount_negative(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new ExperienceReward(-1);
    }
}
