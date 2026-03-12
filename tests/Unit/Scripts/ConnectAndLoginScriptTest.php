<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Scripts;

use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Scripts\ClientContext;
use AqwSocketClient\Scripts\ConnectAndLoginScript;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConnectAndLoginScriptTest extends TestCase
{
    private ClientContext $ctx;
    private ConnectAndLoginScript $script;

    protected function setUp(): void
    {
        $this->ctx = new ClientContext();
        $this->script = new ConnectAndLoginScript(new PlayerName('Hilise'), 'token');
    }

    #[Test]
    public function it_returns_login_command_on_connection_established(): void
    {
        $command = $this->script->handle(new ConnectionEstablishedEvent(), $this->ctx);
        $this->assertInstanceOf(LoginCommand::class, $command);
    }

    #[Test]
    public function it_succeeds_and_stores_socket_id_on_successful_login(): void
    {
        $socketId = new SocketIdentifier(42);
        $this->script->handle(new LoginRespondedEvent(true, $socketId), $this->ctx);

        $this->assertTrue($this->script->isDone());
        $this->assertSame(ScriptResult::Success, $this->script->result());
        $this->assertSame($socketId, $this->ctx->get('socket_id'));
    }

    #[Test]
    public function it_fails_on_failed_login(): void
    {
        $this->script->handle(new LoginRespondedEvent(false, null), $this->ctx);

        $this->assertTrue($this->script->isDone());
        $this->assertSame(ScriptResult::Failed, $this->script->result());
    }

    #[Test]
    public function it_returns_null_on_successful_login_response(): void
    {
        $command = $this->script->handle(new LoginRespondedEvent(true, new SocketIdentifier(1)), $this->ctx);
        $this->assertNull($command);
    }
}
