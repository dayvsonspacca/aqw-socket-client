<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Commands;

use AqwSocketClient\Commands\LoadPlayerInventoryCommand;
use AqwSocketClient\Packet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoadPlayerInventoryCommandTest extends TestCase
{
    private readonly LoadPlayerInventoryCommand $command;

    protected function setUp(): void
    {
        $this->command = new LoadPlayerInventoryCommand(5, 8);
    }

    #[Test]
    public function should_create_first_login_command()
    {
        $this->assertInstanceOf(LoadPlayerInventoryCommand::class, $this->command);
    }

    #[Test]
    public function should_pack_to_correct_first_login_packet()
    {
        $packet = $this->command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertSame($packet->unpacketify(), "%xt%zm%retrieveInventory%5%8%\u{0000}");
    }
}
