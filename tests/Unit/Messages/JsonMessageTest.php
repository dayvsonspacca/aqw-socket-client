<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Messages;

use AqwSocketClient\Enums\JsonMessageType;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Tests\Helpers\MessageGenerator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class JsonMessageTest extends TestCase
{
    #[Test]
    public function it_create_message_when_valid_raw_string(): void
    {
        $message = JsonMessage::from(MessageGenerator::loadInventory());

        $this->assertInstanceOf(JsonMessage::class, $message);
        $this->assertSame($message->type, JsonMessageType::InventoryLoaded);
    }

    #[Test]
    public function it_returns_false_when_invalid_raw_string(): void
    {
        $message = JsonMessage::from(MessageGenerator::domainPolicy());

        $this->assertFalse($message);
    }

    #[Test]
    public function it_returns_false_when_invalid_json_message_command(): void
    {
        $message = JsonMessage::from(
            '{"t":"xt","b":{"r":-1,"o":{"uid":43951,"ItemID":41811,"strES":"he","cmd":"notExpected","sFile":"items/helms/2002AQMageHood.swf","sLink":"2002AQMageHood","sMeta":""}}}',
        );

        $this->assertFalse($message);
    }

    #[Test]
    public function it_return_false_when_data_structure_is_missing_b_o(): void
    {
        $rawMessage = '{"t":"xt"}';
        $message = JsonMessage::from($rawMessage);
        $this->assertFalse($message);

        $rawMessage = '{"t":"xt","b":{"r":-1}}';
        $message = JsonMessage::from($rawMessage);
        $this->assertFalse($message);

        $rawMessage = '{"t":"xt","b": "not an array"}';
        $message = JsonMessage::from($rawMessage);
        $this->assertFalse($message);

        $rawMessage = '{"t":"xt","b":{"r":-1,"o": "not an array"}}';
        $message = JsonMessage::from($rawMessage);
        $this->assertFalse($message);
    }

    #[Test]
    public function it_return_false_when_cmd_is_missing_or_not_a_string(): void
    {
        $rawMessage = '{"t":"xt","b":{"r":-1,"o":{"uid":43951,"ItemID":41811,"strES":"he","sFile":"items/helms/2002AQMageHood.swf","sLink":"2002AQMageHood","sMeta":""}}}';
        $message = JsonMessage::from($rawMessage);
        $this->assertFalse($message);

        $rawMessage = '{"t":"xt","b":{"r":-1,"o":{"uid":43951,"ItemID":41811,"strES":"he","cmd":12345,"sFile":"items/helms/2002AQMageHood.swf","sLink":"2002AQMageHood","sMeta":""}}}';
        $message = JsonMessage::from($rawMessage);
        $this->assertFalse($message);

        $rawMessage = '{"t":"xt","b":{"r":-1,"o":{"uid":43951,"ItemID":41811,"strES":"he","cmd":["wearItem"],"sFile":"items/helms/2002AQMageHood.swf","sLink":"2002AQMageHood","sMeta":""}}}';
        $message = JsonMessage::from($rawMessage);
        $this->assertFalse($message);
    }
}
