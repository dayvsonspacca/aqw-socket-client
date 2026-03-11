<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Identifiers\ItemIdentifier;
use AqwSocketClient\Objects\Quest\ItemRequirement;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ItemRequirementTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $requirement = new ItemRequirement(new ItemIdentifier(123));

        $this->assertInstanceOf(ItemRequirement::class, $requirement);
        $this->assertSame(123, $requirement->itemIdentifier->value);
    }
}
