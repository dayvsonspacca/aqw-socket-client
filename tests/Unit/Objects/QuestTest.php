<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Enums\Tag;
use AqwSocketClient\Objects\ExperienceReward;
use AqwSocketClient\Objects\Identifiers\QuestIdentifier;
use AqwSocketClient\Objects\Names\QuestName;
use AqwSocketClient\Objects\Quest;
use AqwSocketClient\Objects\QuestDescription;
use AqwSocketClient\Objects\QuestRequirements;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class QuestTest extends TestCase
{
    private function makeRequirements(array $overrides = []): QuestRequirements
    {
        return new QuestRequirements(
            $overrides['level'] ?? 0,
            $overrides['reputation'] ?? 0,
            $overrides['classPoints'] ?? 0,
            $overrides['items'] ?? [],
        );
    }

    private function makeQuest(array $overrides = []): Quest
    {
        return new Quest(
            $overrides['identifier'] ?? new QuestIdentifier(868),
            $overrides['name'] ?? new QuestName('Nulgath (Rare)'),
            $overrides['description'] ?? new QuestDescription(
                'Bring me some Mana Energy from the Mana Golem.',
                'AND I\'ve raised your chance of winning!',
            ),
            $overrides['requirements'] ?? $this->makeRequirements(),
            $overrides['rewards'] ?? [],
            $overrides['turnInItems'] ?? [],
            $overrides['tags'] ?? [],
        );
    }

    #[Test]
    public function it_can_create(): void
    {
        $quest = $this->makeQuest();

        $this->assertInstanceOf(Quest::class, $quest);
        $this->assertSame(868, $quest->identifier->value);
        $this->assertSame('Nulgath (Rare)', $quest->name->value);
        $this->assertSame(0, $quest->requirements->level);
        $this->assertEmpty($quest->rewards);
        $this->assertEmpty($quest->turnInItems);
        $this->assertEmpty($quest->tags);
    }

    #[Test]
    public function it_can_create_with_rewards(): void
    {
        $quest = $this->makeQuest([
            'rewards' => [new ExperienceReward(300)],
        ]);

        $this->assertCount(1, $quest->rewards);
        $this->assertInstanceOf(ExperienceReward::class, $quest->rewards[0]);
    }

    #[Test]
    public function it_can_create_with_tags(): void
    {
        $quest = $this->makeQuest(['tags' => [Tag::MemberOnly, Tag::OneTime]]);

        $this->assertContains(Tag::MemberOnly, $quest->tags);
        $this->assertContains(Tag::OneTime, $quest->tags);
    }
}
