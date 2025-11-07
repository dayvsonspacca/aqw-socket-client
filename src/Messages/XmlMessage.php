<?php

declare(strict_types=1);

namespace AqwSocketClient\Messages;

use AqwSocketClient\Interfaces\MessageInterface;
use DOMDocument;

/**
 * This class wraps a raw string and attempts to parse it into a
 * {@see \DOMDocument} object for easy access and interpretation.
 */
class XmlMessage implements MessageInterface
{
    /**
     * @param DOMDocument $dom The parsed XML data of the message, accessible as a DOMDocument.
     */
    private function __construct(public readonly DOMDocument $dom)
    {
    }

    /**
     * Attempts to create an XmlMessage object by loading the raw string as XML.
     *
     * Parsing failures (e.g., incomplete or malformed XML) result in `false`.
     *
     * @param string $message The raw string data received from the socket.
     * @return MessageInterface|false The newly created message object containing the
     * parsed DOMDocument, or **false** on failure to load the XML.
     */
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