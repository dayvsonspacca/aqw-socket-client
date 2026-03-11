<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\GameFileMetadata;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GameFileMetadataTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $metadata = new GameFileMetadata('Dragon1', 'Dragon1.swf');

        $this->assertInstanceOf(GameFileMetadata::class, $metadata);
        $this->assertSame($metadata->link, 'Dragon1');
        $this->assertSame($metadata->file, 'Dragon1.swf');
    }

    #[Test]
    public function should_throw_exception_when_link_empty(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new GameFileMetadata('', 'Dragon1.swf');
    }

    #[Test]
    public function should_throw_exception_when_file_empty(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new GameFileMetadata('Dragon1', '');
    }

    #[Test]
    public function should_throw_exception_when_file_is_not_swf(): void
    {
        $this->expectException(\Psl\Exception\InvariantViolationException::class);

        new GameFileMetadata('Dragon1', 'Dragon1.mp3');
    }
}
