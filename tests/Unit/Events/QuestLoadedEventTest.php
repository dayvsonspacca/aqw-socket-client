<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Events;

use AqwSocketClient\Enums\Tag;
use AqwSocketClient\Events\QuestLoadedEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\Quest\ClassRankRequirement;
use AqwSocketClient\Objects\Quest\ExperienceReward;
use AqwSocketClient\Objects\Quest\GoldReward;
use AqwSocketClient\Objects\Quest\ItemReward;
use AqwSocketClient\Objects\Quest\LevelRequirement;
use AqwSocketClient\Objects\Quest\ReputationRequirement;
use AqwSocketClient\Objects\Quest\ReputationReward;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class QuestLoadedEventTest extends TestCase
{
    #[Test]
    public function it_creates_event_on_correct_message(): void
    {
        $message = JsonMessage::from(MessageGenerator::questLoaded());

        /** @var JsonMessage $message */
        $event = QuestLoadedEvent::from($message);

        $this->assertInstanceOf(QuestLoadedEvent::class, $event);
        $this->assertSame(868, $event->quest->identifier->value);
        $this->assertSame('Nulgath (Rare)', $event->quest->name->value);
    }

    #[Test]
    public function it_maps_rewards_correctly(): void
    {
        $message = JsonMessage::from(MessageGenerator::questLoaded());

        /** @var JsonMessage $message */
        $event = QuestLoadedEvent::from($message);

        $this->assertInstanceOf(QuestLoadedEvent::class, $event);

        $rewards = $event->quest->rewards;
        $this->assertCount(4, $rewards);
        $this->assertInstanceOf(ExperienceReward::class, $rewards[0]);
        $this->assertSame(300, $rewards[0]->amount);
        $this->assertInstanceOf(GoldReward::class, $rewards[1]);
        $this->assertSame(13_000, $rewards[1]->amount);
        $this->assertInstanceOf(ReputationReward::class, $rewards[2]);
        $this->assertSame(300, $rewards[2]->amount);
        $this->assertSame(4, $rewards[2]->faction->identifier->value);
        $this->assertInstanceOf(ItemReward::class, $rewards[3]);
        $this->assertSame(4861, $rewards[3]->itemIdentifier->value);
        $this->assertSame(10, $rewards[3]->rate);
    }

    #[Test]
    public function it_maps_turn_in_items_correctly(): void
    {
        $message = JsonMessage::from(MessageGenerator::questLoaded());

        /** @var JsonMessage $message */
        $event = QuestLoadedEvent::from($message);

        $this->assertInstanceOf(QuestLoadedEvent::class, $event);
        $this->assertCount(1, $event->quest->turnInItems);
        $this->assertSame(15_385, $event->quest->turnInItems[0]->itemIdentifier->value);
        $this->assertSame(5, $event->quest->turnInItems[0]->quantity);
    }

    #[Test]
    public function it_maps_empty_requirements_when_all_zero(): void
    {
        $message = JsonMessage::from(MessageGenerator::questLoaded());

        /** @var JsonMessage $message */
        $event = QuestLoadedEvent::from($message);

        $this->assertInstanceOf(QuestLoadedEvent::class, $event);
        $this->assertEmpty($event->quest->requirements);
    }

    #[Test]
    public function it_maps_requirements_correctly(): void
    {
        $message = JsonMessage::from(MessageGenerator::questLoadedWithTagsAndRequirements());

        /** @var JsonMessage $message */
        $event = QuestLoadedEvent::from($message);

        $this->assertInstanceOf(QuestLoadedEvent::class, $event);

        $requirements = $event->quest->requirements;
        $this->assertCount(3, $requirements);
        $this->assertInstanceOf(LevelRequirement::class, $requirements[0]);
        $this->assertSame(30, $requirements[0]->level->value);
        $this->assertInstanceOf(ReputationRequirement::class, $requirements[1]);
        $this->assertSame(1, $requirements[1]->factionIdentifier->value);
        $this->assertSame(5, $requirements[1]->rank->value);
        $this->assertInstanceOf(ClassRankRequirement::class, $requirements[2]);
        $this->assertSame(42, $requirements[2]->classIdentifier->value);
        $this->assertSame(10, $requirements[2]->rank->value);
    }

    #[Test]
    public function it_maps_tags_correctly(): void
    {
        $message = JsonMessage::from(MessageGenerator::questLoadedWithTagsAndRequirements());

        /** @var JsonMessage $message */
        $event = QuestLoadedEvent::from($message);

        $this->assertInstanceOf(QuestLoadedEvent::class, $event);

        $tags = $event->quest->tags;
        $this->assertCount(2, $tags);
        $this->assertSame(Tag::OneTime, $tags[0]);
        $this->assertSame(Tag::MemberOnly, $tags[1]);
    }

    #[Test]
    public function it_maps_staff_and_guild_tags_correctly(): void
    {
        $message = JsonMessage::from(MessageGenerator::questLoadedWithStaffAndGuildTags());

        /** @var JsonMessage $message */
        $event = QuestLoadedEvent::from($message);

        $this->assertInstanceOf(QuestLoadedEvent::class, $event);

        $tags = $event->quest->tags;
        $this->assertCount(2, $tags);
        $this->assertSame(Tag::StaffOnly, $tags[0]);
        $this->assertSame(Tag::GuildQuest, $tags[1]);
    }

    #[Test]
    public function it_maps_empty_tags_when_all_false(): void
    {
        $message = JsonMessage::from(MessageGenerator::questLoaded());

        /** @var JsonMessage $message */
        $event = QuestLoadedEvent::from($message);

        $this->assertInstanceOf(QuestLoadedEvent::class, $event);
        $this->assertEmpty($event->quest->tags);
    }

    #[Test]
    public function it_returns_null_on_invalid_message(): void
    {
        $message = JsonMessage::from(MessageGenerator::loadInventory());

        /** @var JsonMessage $message */
        $event = QuestLoadedEvent::from($message);

        $this->assertNull($event);
    }

    #[Test]
    public function it_returns_null_when_quests_empty(): void
    {
        $message = JsonMessage::from('{"t":"xt","b":{"r":-1,"o":{"cmd":"getQuests","quests":{}}}}');

        /** @var JsonMessage $message */
        $event = QuestLoadedEvent::from($message);

        $this->assertNull($event);
    }
}
