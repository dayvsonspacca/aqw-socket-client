<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Levels\PlayerLevel;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PlayerLevelTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $level = new PlayerLevel(100);

        $this->assertInstanceOf(PlayerLevel::class, $level);
        $this->assertSame($level->value, 100);
    }

    #[Test]
    public function should_throw_exception_when_value_greater_than_100(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new PlayerLevel(101);
    }

    #[Test]
    public function should_throw_exception_when_value_zero(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new PlayerLevel(0);
    }
}
