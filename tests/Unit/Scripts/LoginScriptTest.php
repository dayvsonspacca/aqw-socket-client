<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Scripts;

use AqwSocketClient\Commands\JoinInitialAreaCommand;
use AqwSocketClient\Commands\LoadPlayerInventoryCommand;
use AqwSocketClient\Commands\LoginCommand;
use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Events\PlayerDetectedEvent;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use AqwSocketClient\Scripts\LoginScript;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoginScriptTest extends TestCase
{
    private LoginScript $script;

    protected function setUp(): void
    {
        $username = 'Hilise';
        $token = md5(random_bytes(1));

        $this->script = new LoginScript($username, $token);
    }

    #[Test]
    public function it_responds_with_login_command_when_connection_established(): void
    {
        $commands = $this->script->handle(new ConnectionEstablishedEvent());

        $command = $commands[0];

        $this->assertInstanceOf(LoginCommand::class, $command);
    }

    #[Test]
    public function it_responds_with_join_initial_area_when_login_responded_is_success(): void
    {
        $commands = $this->script->handle(new LoginRespondedEvent(true, new SocketIdentifier(2)));

        $command = $commands[0];

        $this->assertInstanceOf(JoinInitialAreaCommand::class, $command);
    }

    #[Test]
    public function it_dont_do_anything_to_unrelated_event(): void
    {
        $commands = $this->script->handle(new PlayerDetectedEvent('Hilise'));
        $this->assertEmpty($commands);
    }

    #[Test]
    public function it_dont_load_player_inventory_when_joins_battleon_but_socket_is_null(): void
    {
        $commands = $this->script->handle(new AreaJoinedEvent('battleon', 1, new AreaIdentifier(1), [], []));
        $this->assertEmpty($commands);
    }

    #[Test]
    public function it_dont_load_player_inventory_when_joins_battleon(): void
    {
        $this->script->handle(new LoginRespondedEvent(true, new SocketIdentifier(2)));
        $commands = $this->script->handle(new AreaJoinedEvent('battleon', 1, new AreaIdentifier(1), [], []));

        $command = $commands[0];

        $this->assertInstanceOf(LoadPlayerInventoryCommand::class, $command);
    }

    #[Test]
    public function it_marks_script_as_done_when_ends(): void
    {
        $this->script->handle(new LoginRespondedEvent(true, new SocketIdentifier(2)));
        $this->script->handle(new AreaJoinedEvent('battleon', 1, new AreaIdentifier(1), [], []));

        $this->assertTrue($this->script->isDone());
    }

    #[Test]
    public function it_have_the_expected_events(): void
    {
        $this->assertCount(4, $this->script->handles());

        $this->assertContains(ConnectionEstablishedEvent::class, $this->script->handles());
        $this->assertContains(LoginRespondedEvent::class, $this->script->handles());
        $this->assertContains(AreaJoinedEvent::class, $this->script->handles());
        $this->assertContains(PlayerInventoryLoadedEvent::class, $this->script->handles());
    }
}
