<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\ClassPointsRequirement;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ClassPointsRequirementTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $requirement = new ClassPointsRequirement(50);

        $this->assertInstanceOf(ClassPointsRequirement::class, $requirement);
        $this->assertSame(50, $requirement->classPoints);
    }

    #[Test]
    public function it_can_create_with_zero(): void
    {
        $requirement = new ClassPointsRequirement(0);

        $this->assertSame(0, $requirement->classPoints);
    }

    #[Test]
    public function should_throw_exception_when_class_points_negative(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new ClassPointsRequirement(-1);
    }
}
