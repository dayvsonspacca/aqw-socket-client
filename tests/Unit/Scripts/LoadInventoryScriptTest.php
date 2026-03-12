<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Scripts;

use AqwSocketClient\Commands\LoadPlayerInventoryCommand;
use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use AqwSocketClient\Scripts\ClientContext;
use AqwSocketClient\Scripts\LoadInventoryScript;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoadInventoryScriptTest extends TestCase
{
    private ClientContext $ctx;

    protected function setUp(): void
    {
        $this->ctx = new ClientContext();
        $this->ctx->set('socket_id', new SocketIdentifier(1));
        $this->ctx->set('area_id', new AreaIdentifier(1));
    }

    #[Test]
    public function it_sends_load_inventory_command_on_start(): void
    {
        $script = new LoadInventoryScript();
        $command = $script->start($this->ctx);
        $this->assertInstanceOf(LoadPlayerInventoryCommand::class, $command);
    }

    #[Test]
    public function it_succeeds_when_inventory_loaded_event_arrives(): void
    {
        $script = new LoadInventoryScript();
        $script->handle(new PlayerInventoryLoadedEvent(), $this->ctx);

        $this->assertTrue($script->isDone());
        $this->assertSame(ScriptResult::Success, $script->result());
    }
}
