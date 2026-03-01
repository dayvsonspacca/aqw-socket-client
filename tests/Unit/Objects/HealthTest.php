<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Health;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class HealthTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $identifier = new Health(200);

        $this->assertInstanceOf(Health::class, $identifier);
        $this->assertSame($identifier->value, 200);
        $this->assertSame((string) $identifier, '200');
    }

    #[Test]
    public function should_throw_exception_when_value_equal_zero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Health(-11);
    }
}
