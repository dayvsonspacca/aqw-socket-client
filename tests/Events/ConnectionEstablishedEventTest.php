<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Commands;

use AqwSocketClient\Events\ConnectionEstablishedEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConnectionEstablishedEventTest extends TestCase
{
    #[Test]
    public function should_create_connection_established_event(): void
    {
        $event = new ConnectionEstablishedEvent();

        $this->assertInstanceOf(ConnectionEstablishedEvent::class, $event);
    }
}
