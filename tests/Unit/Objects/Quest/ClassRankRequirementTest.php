<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Identifiers\ClassIdentifier;
use AqwSocketClient\Objects\Levels\Rank;
use AqwSocketClient\Objects\Quest\ClassRankRequirement;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ClassRankRequirementTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $requirement = new ClassRankRequirement(new ClassIdentifier(42), new Rank(5));

        $this->assertInstanceOf(ClassRankRequirement::class, $requirement);
        $this->assertSame(42, $requirement->classIdentifier->value);
        $this->assertSame(5, $requirement->rank->value);
    }
}
