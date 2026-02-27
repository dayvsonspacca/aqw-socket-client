<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use InvalidArgumentException;

final class GameFileMetadata
{
    public function __construct(
        public readonly string $link,
        public readonly string $file,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if ($this->link === '') {
            throw new InvalidArgumentException('The file linkage cant be empty');
        }

        if ($this->file === '') {
            throw new InvalidArgumentException('The game file name cant be empty');
        }

        if (!str_ends_with($this->file, '.swf')) {
            throw new InvalidArgumentException('The game file need do be a .swf file.');
        }
    }
}
