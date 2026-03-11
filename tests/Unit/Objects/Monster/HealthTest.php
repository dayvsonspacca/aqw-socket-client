<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Monster\Health;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class HealthTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $health = new Health(200);

        $this->assertInstanceOf(Health::class, $health);
        $this->assertSame($health->value, 200);
        $this->assertSame((string) $health, '200');
    }

    #[Test]
    public function it_can_create_with_zero(): void
    {
        $health = new Health(0);

        $this->assertSame($health->value, 0);
    }

    #[Test]
    public function should_throw_exception_when_value_negative(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new Health(-11);
    }
}
