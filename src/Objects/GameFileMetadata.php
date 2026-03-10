<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use Psl;
use Psl\Str;
use Psl\Type;

final readonly class GameFileMetadata
{
    public function __construct(
        public readonly string $link,
        public readonly string $file,
    ) {
        Type\non_empty_string()->assert($this->link);
        Type\non_empty_string()->assert($this->file);
        Psl\invariant(Str\ends_with($this->file, '.swf'), 'The game file must be a .swf file, got "%s".', $this->file);
    }
}
