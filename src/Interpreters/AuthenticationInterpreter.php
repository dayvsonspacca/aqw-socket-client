<?php

declare(strict_types=1);

namespace AqwSocketClient\Interpreters;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Events\ConnectionEstablishedEvent;
use AqwSocketClient\Events\LoginRespondedEvent;
use AqwSocketClient\Events\PlayerLoggedOutEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\InterpreterInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\XmlMessage;
use AqwSocketClient\Objects\SocketIdentifier;
use InvalidArgumentException;
use Override;

/**
 * Interprets messages related to the game authentication and session status
 *
 * ### Events:
 * - {@see AqwSocketClient\Events\ConnectionEstablishedEvent}
 * - {@see AqwSocketClient\Events\PlayerLoggedOutEvent}
 * - {@see AqwSocketClient\Events\LoginRespondedEvent}
 */
final class AuthenticationInterpreter implements InterpreterInterface
{
    /**
     * @throws InvalidArgumentException When socket id in message is zero or negative
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

    /** @return EventInterface[] */
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

    /**
     * @throws InvalidArgumentException
     * @return EventInterface[]
     */
    // @mago-ignore analyzer:possibly-undefined-array-index
    private function interpretDelimited(DelimitedMessage $message): array
    {
        $events = [];

        if ($message->type === DelimitedMessageType::LoginResponse) {
            $events[] = new LoginRespondedEvent(
                (bool) $message->data[0],
                new SocketIdentifier((int) $message->data[1]),
            );
        }

        return $events;
    }
}
