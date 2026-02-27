<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Names\Name;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NameTest extends TestCase
{
    #[Test]
    public function it_can_create_name(): void
    {
        $name = new class('Red dragon') extends Name {};

        $this->assertInstanceOf(Name::class, $name);
        $this->assertSame($name->value, 'Red dragon');
    }

    #[Test]
    public function should_throw_exeception_when_name_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new class('') extends Name {};
    }
}
