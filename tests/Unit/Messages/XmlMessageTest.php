<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Messages;

use AqwSocketClient\Messages\XmlMessage;
use AqwSocketClient\Tests\Helpers\MessageGenerator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class XmlMessageTest extends TestCase
{
    #[Test]
    public function it_create_message_when_valid_raw_string(): void
    {
        $message = XmlMessage::from(MessageGenerator::domainPolicy());

        $this->assertInstanceOf(XmlMessage::class, $message);
    }

    #[Test]
    public function it_returns_false_when_invalid_raw_string(): void
    {
        $message = XmlMessage::from(MessageGenerator::loadInventory());

        $this->assertFalse($message);
    }
}
