<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Faction;
use AqwSocketClient\Objects\Identifiers\FactionIdentifier;
use AqwSocketClient\Objects\Names\FactionName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FactionTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $faction = new Faction(new FactionIdentifier(4), new FactionName('Evil'));

        $this->assertInstanceOf(Faction::class, $faction);
        $this->assertSame($faction->identifier->value, 4);
        $this->assertSame($faction->name->value, 'Evil');
    }
}
