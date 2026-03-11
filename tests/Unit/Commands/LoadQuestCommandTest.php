<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Commands;

use AqwSocketClient\Commands\LoadQuestCommand;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\QuestIdentifier;
use AqwSocketClient\Packet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoadQuestCommandTest extends TestCase
{
    private LoadQuestCommand $command;
    private QuestIdentifier $questIdentifier;
    private AreaIdentifier $areaIdentifier;

    protected function setUp(): void
    {
        $this->questIdentifier = new QuestIdentifier(1);
        $this->areaIdentifier = new AreaIdentifier(1);
        $this->command = new LoadQuestCommand($this->areaIdentifier, $this->questIdentifier);
    }

    #[Test]
    public function it_creates_command(): void
    {
        $this->assertInstanceOf(LoadQuestCommand::class, $this->command);
        $this->assertSame($this->questIdentifier, $this->command->questIdentifier);
        $this->assertSame($this->areaIdentifier, $this->command->areaIdentifier);
    }

    #[Test]
    public function should_pack_correct_packet(): void
    {
        $packet = $this->command->pack();

        $this->assertInstanceOf(Packet::class, $packet);
        $this->assertStringContainsString(
            "getQuests%{$this->areaIdentifier}%{$this->questIdentifier}%",
            $packet->unpacketify(),
        );
    }
}
