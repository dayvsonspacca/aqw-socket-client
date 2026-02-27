<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\AlreadyInAreaEvent;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Tests\Helpers\MessageGenerator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AlreadyInAreaEventTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $message = DelimitedMessage::from(MessageGenerator::alreadyInArea());

        /** @var DelimitedMessage $message */
        $event = AlreadyInAreaEvent::from($message);

        $this->assertInstanceOf(AlreadyInAreaEvent::class, $event);
    }

    #[Test]
    public function it_creates_null_on_invalid_messages(): void
    {
        $message = DelimitedMessage::from(MessageGenerator::afk());

        /** @var DelimitedMessage $message */
        $event = AlreadyInAreaEvent::from($message);

        $this->assertNull($event);
    }
}
