<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Faction;
use AqwSocketClient\Objects\Identifiers\FactionIdentifier;
use AqwSocketClient\Objects\Names\FactionName;
use AqwSocketClient\Objects\QuestRewardInterface;
use AqwSocketClient\Objects\ReputationReward;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ReputationRewardTest extends TestCase
{
    private function makeFaction(): Faction
    {
        return new Faction(new FactionIdentifier(4), new FactionName('Evil'));
    }

    #[Test]
    public function it_can_create(): void
    {
        $reward = new ReputationReward(300, $this->makeFaction());

        $this->assertInstanceOf(ReputationReward::class, $reward);
        $this->assertInstanceOf(QuestRewardInterface::class, $reward);
        $this->assertSame(300, $reward->amount);
        $this->assertSame(4, $reward->faction->identifier->value);
        $this->assertSame('Evil', $reward->faction->name->value);
    }

    #[Test]
    public function it_can_create_with_zero(): void
    {
        $reward = new ReputationReward(0, $this->makeFaction());

        $this->assertSame(0, $reward->amount);
    }

    #[Test]
    public function should_throw_exception_when_amount_negative(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new ReputationReward(-1, $this->makeFaction());
    }
}
