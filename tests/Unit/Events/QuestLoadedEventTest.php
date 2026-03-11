<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Events;

use AqwSocketClient\Events\QuestLoadedEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\ExperienceReward;
use AqwSocketClient\Objects\GoldReward;
use AqwSocketClient\Objects\ItemReward;
use AqwSocketClient\Objects\ReputationReward;
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
        $this->assertSame(13000, $rewards[1]->amount);
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
        $this->assertSame(15385, $event->quest->turnInItems[0]->itemIdentifier->value);
        $this->assertSame(5, $event->quest->turnInItems[0]->quantity);
    }

    #[Test]
    public function it_maps_requirements_correctly(): void
    {
        $message = JsonMessage::from(MessageGenerator::questLoaded());

        /** @var JsonMessage $message */
        $event = QuestLoadedEvent::from($message);

        $this->assertInstanceOf(QuestLoadedEvent::class, $event);
        $this->assertSame(0, $event->quest->requirements->level);
        $this->assertSame(0, $event->quest->requirements->reputation);
        $this->assertSame(0, $event->quest->requirements->classPoints);
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
