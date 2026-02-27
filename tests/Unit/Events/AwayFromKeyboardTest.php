<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\AwayFromKeyboardEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Messages\DelimitedMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AwayFromKeyboardTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $message = DelimitedMessage::from(MessageGenerator::afk());

        /** @var DelimitedMessage $message */
        $event = AwayFromKeyboardEvent::from($message);

        $this->assertInstanceOf(AwayFromKeyboardEvent::class, $event);
    }

    #[Test]
    public function it_creates_null_on_invalid_messages(): void
    {
        $message = DelimitedMessage::from(MessageGenerator::exitArea('Hilise'));

        /** @var DelimitedMessage $message */
        $event = AwayFromKeyboardEvent::from($message);

        $this->assertNull($event);
    }
}
