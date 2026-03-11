<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Enums\Tag;
use AqwSocketClient\Objects\Identifiers\QuestIdentifier;
use AqwSocketClient\Objects\Levels\PlayerLevel;
use AqwSocketClient\Objects\Names\QuestName;
use AqwSocketClient\Objects\Quest\ExperienceReward;
use AqwSocketClient\Objects\Quest\LevelRequirement;
use AqwSocketClient\Objects\Quest\Quest;
use AqwSocketClient\Objects\Quest\QuestDescription;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class QuestTest extends TestCase
{
    /**
     * @param array<string, mixed> $overrides
     * @mago-ignore analyzer:mixed-argument
     */
    private function makeQuest(array $overrides = []): Quest
    {
        return new Quest(
            $overrides['identifier'] ?? new QuestIdentifier(868),
            $overrides['name'] ?? new QuestName('Nulgath (Rare)'),
            $overrides['description'] ?? new QuestDescription(
                'Bring me some Mana Energy from the Mana Golem.',
                'AND I\'ve raised your chance of winning!',
            ),
            $overrides['requirements'] ?? [],
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
        $this->assertEmpty($quest->requirements);
        $this->assertEmpty($quest->rewards);
        $this->assertEmpty($quest->turnInItems);
        $this->assertEmpty($quest->tags);
    }

    #[Test]
    public function it_can_create_with_requirements(): void
    {
        $quest = $this->makeQuest([
            'requirements' => [new LevelRequirement(new PlayerLevel(10))],
        ]);

        $this->assertCount(1, $quest->requirements);
        $this->assertInstanceOf(LevelRequirement::class, $quest->requirements[0]);
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
