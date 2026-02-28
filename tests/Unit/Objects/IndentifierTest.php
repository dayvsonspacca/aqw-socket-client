<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\Identifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IndentifierTest extends TestCase
{
    #[Test]
    public function it_can_create_identifier(): void
    {
        $identifier = new SocketIdentifier(200);

        $this->assertInstanceOf(Identifier::class, $identifier);
        $this->assertSame($identifier->value, 200);
        $this->assertSame((string) $identifier, '200');
    }

    #[Test]
    public function should_throw_exeception_when_value_equal_zero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SocketIdentifier(0);
    }

    #[Test]
    public function should_throw_exeception_when_value_negative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AreaIdentifier(-23);
    }
}
