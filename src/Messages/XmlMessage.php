<?php

declare(strict_types=1);

namespace AqwSocketClient\Messages;

use AqwSocketClient\Interfaces\MessageInterface;
use DOMDocument;

class XmlMessage implements MessageInterface
{
    private function __construct(public readonly DOMDocument $dom)
    {
    }

    public static function fromString(string $message): MessageInterface|false
    {
        $dom = new DOMDocument();
        
        $success = @$dom->loadXML($message);
        if (!$success) {
            return false;
        }

        return new self($dom);
    }
}
