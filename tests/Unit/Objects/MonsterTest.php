<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\GameFileMetadata;
use AqwSocketClient\Objects\Health;
use AqwSocketClient\Objects\Identifiers\MonsterIdentifier;
use AqwSocketClient\Objects\Levels\MonsterLevel;
use AqwSocketClient\Objects\Monster;
use AqwSocketClient\Objects\Names\MonsterName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MonsterTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $monster = new Monster(
            new MonsterIdentifier(1),
            new MonsterName('Dragon'),
            new MonsterLevel(50),
            new Health(5000),
            new GameFileMetadata('Dragon', 'Dragon.swf'),
        );

        $this->assertInstanceOf(Monster::class, $monster);
        $this->assertSame($monster->identifier->value, 1);
        $this->assertSame($monster->name->value, 'Dragon');
        $this->assertSame($monster->level->value, 50);
        $this->assertSame($monster->health->value, 5000);
        $this->assertSame($monster->metadata->link, 'Dragon');
        $this->assertSame($monster->metadata->file, 'Dragon.swf');
    }
}
