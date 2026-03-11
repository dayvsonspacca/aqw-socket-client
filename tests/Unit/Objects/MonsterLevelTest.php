<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Levels\MonsterLevel;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MonsterLevelTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $level = new MonsterLevel(255);

        $this->assertInstanceOf(MonsterLevel::class, $level);
        $this->assertSame($level->value, 255);
    }

    #[Test]
    public function should_throw_exception_when_value_greater_than_255(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new MonsterLevel(256);
    }

    #[Test]
    public function should_throw_exception_when_value_zero(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new MonsterLevel(0);
    }
}
