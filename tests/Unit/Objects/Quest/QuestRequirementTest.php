<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Identifiers\QuestIdentifier;
use AqwSocketClient\Objects\Quest\QuestRequirement;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class QuestRequirementTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $requirement = new QuestRequirement(new QuestIdentifier(868));

        $this->assertInstanceOf(QuestRequirement::class, $requirement);
        $this->assertSame(868, $requirement->questIdentifier->value);
    }
}
