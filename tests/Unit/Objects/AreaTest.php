<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Area;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\RoomIdentifier;
use AqwSocketClient\Objects\Names\AreaName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AreaTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $area = new Area(new AreaIdentifier(1), new AreaName('battleon'), new RoomIdentifier(1));

        $this->assertInstanceOf(Area::class, $area);
        $this->assertSame($area->identifier->value, 1);
        $this->assertSame($area->name->value, 'battleon');
        $this->assertSame($area->room->value, 1);
    }
}
