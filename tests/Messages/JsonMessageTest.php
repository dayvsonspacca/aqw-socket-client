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

        $rawMessage = '{"t":"xt","b":{"r":-1,"o":{"uid":43951,"ItemID":41811,"strES":"he","cmd":"wearItem","sFile":"items/helms/2002AQMageHood.swf","sLink":"2002AQMageHood","sMeta":""}}}';
        $message    = JsonMessage::fromString($rawMessage);

        $this->assertInstanceOf(JsonMessage::class, $message);
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
}
