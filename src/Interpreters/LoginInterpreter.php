<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Events\{ConnectionEstabilishedEvent, LoginResponseEvent};
use AqwSocketClient\Interfaces\{InterpreterInterface, MessageInterface};
use AqwSocketClient\Messages\{DelimitedMessage, XmlMessage};

/**
 * An interpreter responsible for parsing incoming server messages that are
 * strictly related to the **initial socket connection and client login** process.
 *
 * It handles the cross-domain policy message and the login response message.
 */
class LoginInterpreter implements InterpreterInterface
{
    /** @var EventInterface[] $events */
    private array $events = [];

    /**
     * Attempts to convert a server message (XML or Delimited) into
     * relevant events during the connection and login phase.
     *
     * Currently handles:
     * - The **cross-domain-policy** XML message, resulting in a {@see AqwSocketClient\Events\ConnectionEstabilishedEvent}.
     * - The **LoginResponse** delimited message, resulting in a {@see AqwSocketClient\Events\LoginResponseEvent}.
     *
     * @param MessageInterface $message The raw, uninterpreted message object.
     * @return EventInterface[] The list of {@see AqwSocketClient\Interfaces\EventInterface} objects generated from the message.
     */
    public function interpret(MessageInterface $message): array
    {
        match ($message::class) {
            XmlMessage::class => $this->interpretXml($message),
            DelimitedMessage::class => $this->interpretDelimited($message),
            default => null
        };
        
        return $this->events;
    }

    private function interpretXml(XmlMessage $message)
    {
        if ($message->dom->firstChild?->nodeName === 'cross-domain-policy') {
            $this->events[] = new ConnectionEstabilishedEvent();
        }
    }

    private function interpretDelimited(DelimitedMessage $message)
    {
        if ($message->type === DelimitedMessageType::LoginResponse) {
            $this->events[] = new LoginResponseEvent((bool) $message->data[0] ?? false);
        }
    }
}
