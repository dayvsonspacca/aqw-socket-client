<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Events\AreaNotAvaliableEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Messages\DelimitedMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AreaNotAvaliableEventTest extends TestCase
{
    #[Test]
    public function it_create_event_on_correct_messages(): void
    {
        $message = DelimitedMessage::from(MessageGenerator::areaNotAvaliabel());

        /** @var DelimitedMessage $message */
        $event = AreaNotAvaliableEvent::from($message);

        $this->assertInstanceOf(AreaNotAvaliableEvent::class, $event);
    }

    #[Test]
    public function it_creates_null_on_invalid_messages(): void
    {
        $message = DelimitedMessage::from(MessageGenerator::afk());

        /** @var DelimitedMessage $message */
        $event = AreaNotAvaliableEvent::from($message);

        $this->assertNull($event);
    }
}
