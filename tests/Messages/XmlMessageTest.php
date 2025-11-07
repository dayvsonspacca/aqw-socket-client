<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Messages;

use AqwSocketClient\Messages\XmlMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class XmlMessageTest extends TestCase
{
    #[Test]
    public function should_create_xml_message()
    {
        $rawMessage = "<cross-domain-policy><allow-access-from domain='*' to-ports='5591' /></cross-domain-policy>";
        $message    = XmlMessage::fromString($rawMessage);

        $this->assertInstanceOf(XmlMessage::class, $message);
    }

    #[Test]
    public function should_return_false_when_cant_parse_to_xml()
    {
        $rawMessage = '{"msg": "hello"}';
        $message    = XmlMessage::fromString($rawMessage);

        $this->assertFalse($message);
    }
}
