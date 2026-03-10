<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\QuestRequirements;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class QuestRequirementsTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $requirements = new QuestRequirements(10, 300, 0, []);

        $this->assertInstanceOf(QuestRequirements::class, $requirements);
        $this->assertSame(10, $requirements->level);
        $this->assertSame(300, $requirements->reputation);
        $this->assertSame(0, $requirements->classPoints);
        $this->assertEmpty($requirements->items);
    }

    #[Test]
    public function it_can_create_with_all_zeros(): void
    {
        $requirements = new QuestRequirements(0, 0, 0, []);

        $this->assertSame(0, $requirements->level);
        $this->assertSame(0, $requirements->reputation);
        $this->assertSame(0, $requirements->classPoints);
    }

    #[Test]
    public function should_throw_exception_when_level_negative(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new QuestRequirements(-1, 0, 0, []);
    }

    #[Test]
    public function should_throw_exception_when_reputation_negative(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new QuestRequirements(0, -1, 0, []);
    }

    #[Test]
    public function should_throw_exception_when_class_points_negative(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new QuestRequirements(0, 0, -1, []);
    }
}
