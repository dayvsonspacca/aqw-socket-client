<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Scripts;

use AqwSocketClient\Commands\JoinInitialAreaCommand;
use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Objects\Area\Area;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\RoomIdentifier;
use AqwSocketClient\Objects\Names\AreaName;
use AqwSocketClient\Scripts\ClientContext;
use AqwSocketClient\Scripts\JoinBattleonScript;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class JoinBattleonScriptTest extends TestCase
{
    private ClientContext $ctx;
    private JoinBattleonScript $script;

    protected function setUp(): void
    {
        $this->ctx = new ClientContext();
        $this->script = new JoinBattleonScript();
    }

    #[Test]
    public function it_sends_join_initial_area_command_on_start(): void
    {
        $command = $this->script->start($this->ctx);
        $this->assertInstanceOf(JoinInitialAreaCommand::class, $command);
    }

    #[Test]
    public function it_succeeds_and_stores_area_id_when_battleon_is_joined(): void
    {
        $areaId = new AreaIdentifier(1);
        $event = new AreaJoinedEvent(
            new Area($areaId, new AreaName('battleon'), new RoomIdentifier(1))
        );

        $this->script->handle($event, $this->ctx);

        $this->assertTrue($this->script->isDone());
        $this->assertSame(ScriptResult::Success, $this->script->result());
        $this->assertSame($areaId, $this->ctx->get('area_id'));
    }

    #[Test]
    public function it_does_not_succeed_when_non_battleon_area_is_joined(): void
    {
        $event = new AreaJoinedEvent(
            new Area(new AreaIdentifier(2), new AreaName('othermap'), new RoomIdentifier(1))
        );

        $this->script->handle($event, $this->ctx);

        $this->assertFalse($this->script->isDone());
    }
}
