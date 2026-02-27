<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\GameFileMetadata;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GameFileMetadataTest extends TestCase
{
    #[Test]
    public function it_can_create_game_file_metadata(): void
    {
        $identifier = new GameFileMetadata('Dragon1', 'Dragon1.swf');

        $this->assertInstanceOf(GameFileMetadata::class, $identifier);
        $this->assertSame($identifier->link, 'Dragon1');
        $this->assertSame($identifier->file, 'Dragon1.swf');
    }

    #[Test]
    public function should_throw_exeception_when_link_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GameFileMetadata('', 'Dragon1.sfw');
    }

    #[Test]
    public function should_throw_exeception_when_file_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GameFileMetadata('Dragon1', '');
    }

    #[Test]
    public function should_throw_exeception_when_file_is_not_swf(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GameFileMetadata('Dragon1', 'Dragon1');
    }
}
