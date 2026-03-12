<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Scripts;

use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Objects\Area\Area;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\RoomIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use AqwSocketClient\Objects\Names\AreaName;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Scripts\ClientContext;
use AqwSocketClient\Scripts\LoginScript;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoginScriptTest extends TestCase
{
    private LoginScript $script;
    private ClientContext $ctx;

    protected function setUp(): void
    {
        $this->ctx = new ClientContext();
        $this->script = new LoginScript(new PlayerName('Hilise'), 'token');
    }

    private function runFullSequence(): void
    {
        $socketId = new SocketIdentifier(1);
        $areaId = new AreaIdentifier(1);

        $this->script->handle(new ConnectionEstablishedEvent(), $this->ctx);
        $this->script->handle(new LoginRespondedEvent(true, $socketId), $this->ctx);
        $this->script->handle(
            new AreaJoinedEvent(new Area($areaId, new AreaName('battleon'), new RoomIdentifier(1))),
            $this->ctx,
        );
        $this->script->handle(new PlayerInventoryLoadedEvent(), $this->ctx);
    }

    #[Test]
    public function it_succeeds_after_full_login_sequence(): void
    {
        $this->runFullSequence();

        $this->assertTrue($this->script->isDone());
        $this->assertSame(ScriptResult::Success, $this->script->result());
    }

    #[Test]
    public function it_fails_when_login_responded_not_success(): void
    {
        $this->script->handle(new ConnectionEstablishedEvent(), $this->ctx);
        $this->script->handle(new LoginRespondedEvent(false, null), $this->ctx);

        $this->assertTrue($this->script->isDone());
        $this->assertSame(ScriptResult::Failed, $this->script->result());
    }

    #[Test]
    public function it_stores_socket_id_in_context(): void
    {
        $socketId = new SocketIdentifier(42);
        $this->script->handle(new ConnectionEstablishedEvent(), $this->ctx);
        $this->script->handle(new LoginRespondedEvent(true, $socketId), $this->ctx);

        $this->assertSame($socketId, $this->ctx->get('socket_id'));
    }

    #[Test]
    public function it_stores_area_id_in_context(): void
    {
        $areaId = new AreaIdentifier(7);
        $socketId = new SocketIdentifier(1);

        $this->script->handle(new ConnectionEstablishedEvent(), $this->ctx);
        $this->script->handle(new LoginRespondedEvent(true, $socketId), $this->ctx);
        $this->script->handle(
            new AreaJoinedEvent(new Area($areaId, new AreaName('battleon'), new RoomIdentifier(1))),
            $this->ctx,
        );

        $this->assertSame($areaId, $this->ctx->get('area_id'));
    }
}
