<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Events\PlayerLoggedOutEvent;
use AqwSocketClient\Interfaces\InterpreterInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\XmlMessage;
use Override;

/**
 * An interpreter responsible for parsing incoming server messages that are
 * strictly related to the **connection, login, and logout** processes.
 *
 * It handles the cross-domain policy message, the login response message,
 * and the logout confirmation message.
 */
final class AuthenticationInterpreter implements InterpreterInterface
{
    /**
     * Attempts to convert a server message (XML or Delimited) into
     * relevant events during the authentication lifecycle.
     *
     * Currently handles:
     * - The **cross-domain-policy** XML message, resulting in a {@see ConnectionEstablishedEvent}.
     * - The **logout** XML message, resulting in a {@see PlayerLoggedOutEvent}.
     * - The **LoginResponse** delimited message, resulting in a {@see LoginRespondedEvent}.
     *
     * @param MessageInterface $message The raw, uninterpreted message object.
     * @return array The list of {@see \AqwSocketClient\Interfaces\EventInterface} objects generated from the message.
     */
    #[Override]
    public function interpret(MessageInterface $message): array
    {
        return match ($message::class) {
            XmlMessage::class => $this->interpretXml($message),
            DelimitedMessage::class => $this->interpretDelimited($message),
            default => [],
        };
    }

    private function interpretXml(XmlMessage $message): array
    {
        $events = [];

        if ($message->dom->firstChild?->nodeName === 'cross-domain-policy') {
            $events[] = new ConnectionEstablishedEvent();
        }

        $action = $message->dom->getElementsByTagName('body')->item(0)?->getAttribute('action');
        if ($action === 'logout') {
            $events[] = new PlayerLoggedOutEvent();
        }

        return $events;
    }

    private function interpretDelimited(DelimitedMessage $message): array
    {
        $events = [];

        if ($message->type === DelimitedMessageType::LoginResponse) {
            $events[] = new LoginRespondedEvent((bool) $message->data[0] ?? false, (int) $message->data[1] ?? null);
        }

        return $events;
    }
}
