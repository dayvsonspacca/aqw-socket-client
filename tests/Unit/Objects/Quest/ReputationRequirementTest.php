<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Faction;
use AqwSocketClient\Objects\Identifiers\FactionIdentifier;
use AqwSocketClient\Objects\Names\FactionName;
use AqwSocketClient\Objects\Quest\ReputationRequirement;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ReputationRequirementTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $faction = new Faction(new FactionIdentifier(4), new FactionName('Evil'));
        $requirement = new ReputationRequirement(300, $faction);

        $this->assertInstanceOf(ReputationRequirement::class, $requirement);
        $this->assertSame(300, $requirement->reputation);
        $this->assertSame(4, $requirement->faction->identifier->value);
    }

    #[Test]
    public function it_can_create_with_zero(): void
    {
        $faction = new Faction(new FactionIdentifier(4), new FactionName('Evil'));
        $requirement = new ReputationRequirement(0, $faction);

        $this->assertSame(0, $requirement->reputation);
    }

    #[Test]
    public function should_throw_exception_when_reputation_negative(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        $faction = new Faction(new FactionIdentifier(4), new FactionName('Evil'));
        new ReputationRequirement(-1, $faction);
    }
}
