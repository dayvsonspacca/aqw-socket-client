<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Messages;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Messages\DelimitedMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DelimitedMessageTest extends TestCase
{
    #[Test]
    public function should_create_delimited_message()
    {
        $rawMessage = '%xt%server%-1%You joined "battleon-2"%';
        $message    = DelimitedMessage::fromString($rawMessage);

        $this->assertInstanceOf(DelimitedMessage::class, $message);
        $this->assertSame($message->type, DelimitedMessageType::Server);
    }

    #[Test]
    public function should_return_false_when_cant_parse_to_delimited()
    {
        $rawMessage = '{"msg": "hello"}';
        $message    = DelimitedMessage::fromString($rawMessage);

        $this->assertFalse($message);
    }

    #[Test]
    public function should_return_false_when_cant_parse_the_delimited_type()
    {
        $rawMessage = '%xt%artixsupercommand%-1%You joined "battleon-2"%';
        $message    = DelimitedMessage::fromString($rawMessage);

        $this->assertFalse($message);
    }
}
