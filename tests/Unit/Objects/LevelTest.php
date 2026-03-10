<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Levels\Level;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LevelTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $level = new readonly class(50) extends Level {};

        $this->assertInstanceOf(Level::class, $level);
        $this->assertSame($level->value, 50);
        $this->assertSame((string) $level, '50');
    }

    #[Test]
    public function should_throw_exception_when_value_equal_zero(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new readonly class(0) extends Level {};
    }

    #[Test]
    public function should_throw_exception_when_value_negative(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new readonly class(-25) extends Level {};
    }
}
