<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Commands\LoadPlayerInventoryCommand;
use AqwSocketClient\Objects\AreaIdentifier;
use AqwSocketClient\Objects\SocketIdentifier;
use AqwSocketClient\Packet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoadPlayerInventoryCommandTest extends TestCase
{
    private LoadPlayerInventoryCommand $command;

    protected function setUp(): void
    {
        $this->command = new LoadPlayerInventoryCommand(
            areaId: new AreaIdentifier(42),
            socketId: new SocketIdentifier(7),
        );
    }

    #[Test]
    public function should_create_load_player_inventory_command(): void
    {
        $this->assertInstanceOf(LoadPlayerInventoryCommand::class, $this->command);
        $this->assertSame(42, $this->command->areaId->value);
        $this->assertSame(7, $this->command->socketId->value);
    }

    #[Test]
    public function should_pack_correct_packet(): void
    {
        $packet = $this->command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertSame("%xt%zm%retrieveInventory%42%7%\u{0000}", $packet->unpacketify());
    }
}
