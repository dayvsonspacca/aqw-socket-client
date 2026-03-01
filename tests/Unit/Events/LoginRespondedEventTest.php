<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use AqwSocketClient\Objects\Names\PlayerName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoginRespondedEventTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $message = DelimitedMessage::from(MessageGenerator::loginReponded(
            new PlayerName('Hilise'),
            new SocketIdentifier(1080),
        ));

        /** @var DelimitedMessage $message */
        $event = LoginRespondedEvent::from($message);

        $this->assertInstanceOf(LoginRespondedEvent::class, $event);
        $this->assertSame($event->socketId->value, 1080);
        $this->assertTrue($event->success);
    }

    #[Test]
    public function it_creates_null_on_invalid_messages(): void
    {
        $message = JsonMessage::from(MessageGenerator::loadInventory());

        /** @var JsonMessage $message */
        $event = LoginRespondedEvent::from($message);

        $this->assertNull($event);
    }
}
