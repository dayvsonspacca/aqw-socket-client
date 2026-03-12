<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects\Quest;

use Psl\Type;

final readonly class QuestDescription
{
    public function __construct(
        public readonly string $text,
        public readonly ?string $completionText = null,
    ) {
        Type\non_empty_string()->assert($this->text);

        if ($this->completionText !== null) {
            Type\non_empty_string()->assert($this->completionText);
        }
    }
}
