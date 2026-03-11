<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Identifiers\FactionIdentifier;
use AqwSocketClient\Objects\Levels\Rank;
use AqwSocketClient\Objects\Quest\ReputationRequirement;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ReputationRequirementTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $requirement = new ReputationRequirement(new FactionIdentifier(4), new Rank(5));

        $this->assertInstanceOf(ReputationRequirement::class, $requirement);
        $this->assertSame(4, $requirement->factionIdentifier->value);
        $this->assertSame(5, $requirement->rank->value);
    }
}
