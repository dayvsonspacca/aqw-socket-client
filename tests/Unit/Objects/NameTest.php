<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Names\Name;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NameTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $name = new readonly class('Red dragon') extends Name {};

        $this->assertInstanceOf(Name::class, $name);
        $this->assertSame($name->value, 'Red dragon');
        $this->assertSame((string) $name, 'Red dragon');
    }

    #[Test]
    public function should_throw_exception_when_name_empty(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new readonly class('') extends Name {};
    }
}
