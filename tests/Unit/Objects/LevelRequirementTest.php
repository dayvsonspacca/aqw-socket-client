<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\LevelRequirement;
use AqwSocketClient\Objects\Levels\PlayerLevel;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LevelRequirementTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $requirement = new LevelRequirement(new PlayerLevel(10));

        $this->assertInstanceOf(LevelRequirement::class, $requirement);
        $this->assertSame(10, $requirement->level->value);
    }
}
