<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Levels\Rank;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RankTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $rank = new Rank(10);

        $this->assertInstanceOf(Rank::class, $rank);
        $this->assertSame($rank->value, 10);
    }

    #[Test]
    public function should_throw_exception_when_value_greater_than_10(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new Rank(11);
    }

    #[Test]
    public function should_throw_exception_when_value_zero(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new Rank(0);
    }
}
