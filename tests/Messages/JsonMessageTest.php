<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Messages;

use AqwSocketClient\Messages\{JsonMessage};
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class JsonMessageTest extends TestCase
{
    #[Test]
    public function should_create_json_message()
    {
        $rawMessage = '{"t":"xt","b":{"r":-1,"o":{"uid":43951,"ItemID":41811,"strES":"he","cmd":"equipItem","sFile":"items/helms/2002AQMageHood.swf","sLink":"2002AQMageHood","sMeta":""}}}';
        $message    = JsonMessage::fromString($rawMessage);

        $this->assertInstanceOf(JsonMessage::class, $message);
        $this->assertSame('equipItem', $message->data['cmd']);

        $rawMessage = '{"t":"xt","b":{"r":-1,"o":{"uid":43951,"ItemID":41811,"strES":"he","cmd":"wearItem","sFile":"items/helms/2002AQMageHood.swf","sLink":"2002AQMageHood","sMeta":""}}}';
        $message    = JsonMessage::fromString($rawMessage);

        $this->assertInstanceOf(JsonMessage::class, $message);
        $this->assertSame('wearItem', $message->data['cmd']);
    }

    #[Test]
    public function should_return_false_when_cant_parse_to_json()
    {
        $rawMessage = "<cross-domain-policy><allow-access-from domain='*' to-ports='5591' /></cross-domain-policy>";
        $message    = JsonMessage::fromString($rawMessage);

        $this->assertFalse($message);
    }

    #[Test]
    public function should_return_false_because_cmd_in_json_is_not_one_expected()
    {
        $rawMessage = '{"t":"xt","b":{"r":-1,"o":{"uid":43951,"ItemID":41811,"strES":"he","cmd":"notExpected","sFile":"items/helms/2002AQMageHood.swf","sLink":"2002AQMageHood","sMeta":""}}}';
        $message    = JsonMessage::fromString($rawMessage);

        $this->assertFalse($message);
    }

    #[Test]
    public function should_return_false_when_data_structure_is_missing_b_o()
    {
        $rawMessage = '{"t":"xt"}';
        $message    = JsonMessage::fromString($rawMessage);
        $this->assertFalse($message);

        $rawMessage = '{"t":"xt","b":{"r":-1}}';
        $message    = JsonMessage::fromString($rawMessage);
        $this->assertFalse($message);

        $rawMessage = '{"t":"xt","b": "not an array"}';
        $message    = JsonMessage::fromString($rawMessage);
        $this->assertFalse($message);

        $rawMessage = '{"t":"xt","b":{"r":-1,"o": "not an array"}}';
        $message    = JsonMessage::fromString($rawMessage);
        $this->assertFalse($message);
    }

    #[Test]
    public function should_return_false_when_cmd_is_missing_or_not_a_string()
    {
        $rawMessage = '{"t":"xt","b":{"r":-1,"o":{"uid":43951,"ItemID":41811,"strES":"he","sFile":"items/helms/2002AQMageHood.swf","sLink":"2002AQMageHood","sMeta":""}}}';
        $message    = JsonMessage::fromString($rawMessage);
        $this->assertFalse($message);

        $rawMessage = '{"t":"xt","b":{"r":-1,"o":{"uid":43951,"ItemID":41811,"strES":"he","cmd":12345,"sFile":"items/helms/2002AQMageHood.swf","sLink":"2002AQMageHood","sMeta":""}}}';
        $message    = JsonMessage::fromString($rawMessage);
        $this->assertFalse($message);

        $rawMessage = '{"t":"xt","b":{"r":-1,"o":{"uid":43951,"ItemID":41811,"strES":"he","cmd":["wearItem"],"sFile":"items/helms/2002AQMageHood.swf","sLink":"2002AQMageHood","sMeta":""}}}';
        $message    = JsonMessage::fromString($rawMessage);
        $this->assertFalse($message);
    }
}
