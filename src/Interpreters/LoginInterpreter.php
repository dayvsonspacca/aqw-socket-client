<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Events\ConnectionEstabilishedEvent;
use AqwSocketClient\Events\LoginResponseEvent;
use AqwSocketClient\Interfaces\{MessageInterface, InterpreterInterface};
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\XmlMessage;

/**
 * An interpreter responsible for parsing incoming server messages that are
 * strictly related to the **initial socket connection and client login** process.
 *
 * It handles the cross-domain policy message and the login response message.
 */
class LoginInterpreter implements InterpreterInterface
{
    /**
     * Attempts to convert a server message (XML or Delimited) into
     * relevant events during the connection and login phase.
     *
     * Currently handles:
     * - The **cross-domain-policy** XML message, resulting in a {@see AqwSocketClient\Events\ConnectionEstabilishedEvent}.
     * - The **LoginResponse** delimited message, resulting in a {@see AqwSocketClient\Events\LoginResponseEvent}.
     *
     * @param MessageInterface $message The raw, uninterpreted message object.
     * @return array The list of {@see AqwSocketClient\Interfaces\EventInterface} objects generated from the message.
     */
    public function interpret(MessageInterface $message): array
    {
        $events = [];

        if ($message instanceof XmlMessage) {
            if ($message->dom->firstChild?->nodeName === 'cross-domain-policy') {
                $events[] = new ConnectionEstabilishedEvent();
            }
        } else if ($message instanceof DelimitedMessage) {
            if ($message->type === DelimitedMessageType::LoginResponse) {
                $events[] = new LoginResponseEvent((bool) $message->data[0] ?? false);
            }
        }

        return $events;
    }
}