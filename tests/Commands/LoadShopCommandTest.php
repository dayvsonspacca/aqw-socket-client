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
        $this->command = new LoadShopCommand(areaId: 42, shopId: 15);
    }

    #[Test]
    public function should_create_load_shop_command(): void
    {
        $this->assertInstanceOf(LoadShopCommand::class, $this->command);
        $this->assertSame(42, $this->command->areaId);
        $this->assertSame(15, $this->command->shopId);
    }

    #[Test]
    public function should_pack_correct_packet(): void
    {
        $packet = $this->command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertSame("%xt%zm%loadShop%42%15%\u{0000}", $packet->unpacketify());
    }
}
