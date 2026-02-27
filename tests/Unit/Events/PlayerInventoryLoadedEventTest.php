<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Tests\Helpers\MessageGenerator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PlayerInventoryLoadedEventTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $message = JsonMessage::from(MessageGenerator::loadInventory());

        /** @var JsonMessage $message */
        $event = PlayerInventoryLoadedEvent::from($message);

        $this->assertInstanceOf(PlayerInventoryLoadedEvent::class, $event);
    }

    #[Test]
    public function it_creates_null_on_invalid_messages(): void
    {
        $message = JsonMessage::from(MessageGenerator::moveToArea('battleon', new AreaIdentifier(5)));

        /** @var JsonMessage $message */
        $event = PlayerInventoryLoadedEvent::from($message);

        $this->assertNull($event);
    }
}
