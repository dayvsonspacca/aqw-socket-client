<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Commands;

use AqwSocketClient\Commands\LoadShopCommand;
use AqwSocketClient\Packet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoadShopCommandTest extends TestCase
{
    private readonly LoadShopCommand $command;

    protected function setUp(): void
    {
        $this->command = new LoadShopCommand(5, 25);
    }

    #[Test]
    public function should_create_load_shop_command()
    {
        $this->assertInstanceOf(LoadShopCommand::class, $this->command);
    }

    #[Test]
    public function should_pack_to_load_shop_command()
    {
        $packet = $this->command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertSame($packet->unpacketify(), "%xt%zm%loadShop%5%25%\u{0000}");
    }
}
