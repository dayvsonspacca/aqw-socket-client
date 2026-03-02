<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Messages;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Objects\Names\PlayerName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DelimitedMessageTest extends TestCase
{
    #[Test]
    public function it_create_message_when_valid_raw_string(): void
    {
        $message = DelimitedMessage::from(MessageGenerator::exitArea(new PlayerName('Hilise')));

        $this->assertInstanceOf(DelimitedMessage::class, $message);
        $this->assertSame($message->type, DelimitedMessageType::ExitArea);
    }

    #[Test]
    public function it_returns_false_when_invalid_raw_string(): void
    {
        $message = DelimitedMessage::from(MessageGenerator::loadInventory());

        $this->assertFalse($message);
    }

    #[Test]
    public function it_returns_false_when_invalid_delimited_message_command(): void
    {
        $message = DelimitedMessage::from('%xt%artixsupercommand%-1%You joined "battleon-2"%');

        $this->assertFalse($message);
    }
}
