<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Events;

use AqwSocketClient\Events\MonstersDetectedEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Messages\JsonMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MonstersDetectedEventTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $message = JsonMessage::from(MessageGenerator::monstersDetected());

        /** @var JsonMessage $message */

        $event = MonstersDetectedEvent::from($message);
        $this->assertInstanceOf(MonstersDetectedEvent::class, $event);
        $this->assertCount(1, $event->monsters);
    }

    #[Test]
    public function it_creates_null_on_invalid_messages(): void
    {
        $message = JsonMessage::from(MessageGenerator::loadInventory());

        /** @var JsonMessage $message */
        $event = MonstersDetectedEvent::from($message);

        $this->assertNull($event);
    }

    #[Test]
    public function it_correctly_maps_monster_health_when_branch_order_differs_from_mondef(): void
    {
        $message = JsonMessage::from(MessageGenerator::monstersDetectedOutOfOrder());

        /** @var JsonMessage $message */
        $event = MonstersDetectedEvent::from($message);

        $this->assertInstanceOf(MonstersDetectedEvent::class, $event);
        $this->assertCount(2, $event->monsters);

        $redDragon = null;
        $zombie = null;
        foreach ($event->monsters as $monster) {
            if ($monster->identifier->value === 14) {
                $redDragon = $monster;
            }
            if ($monster->identifier->value === 7) {
                $zombie = $monster;
            }
        }

        $this->assertNotNull($redDragon);
        $this->assertNotNull($zombie);

        $this->assertSame(30_000, $redDragon->health->value);
        $this->assertSame(5000, $zombie->health->value);
    }

    #[Test]
    public function it_ignores_monster_in_mondef_without_corresponding_branch(): void
    {
        $message = JsonMessage::from(MessageGenerator::monstersDetectedWithOrphanMondef());

        /** @var JsonMessage $message */
        $event = MonstersDetectedEvent::from($message);

        $this->assertInstanceOf(MonstersDetectedEvent::class, $event);

        $this->assertCount(1, $event->monsters);
    }

    #[Test]
    public function it_returns_null_when_mondef_or_monbranch_not_defined(): void
    {
        $message = JsonMessage::from(MessageGenerator::monstersDetectedWithouMonDef());

        /** @var JsonMessage $message */
        $event = MonstersDetectedEvent::from($message);

        $this->assertNull($event);
    }
}
