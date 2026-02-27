<?php

declare(strict_types=1);

namespace AqwSocketClient\Clients;

use AqwSocketClient\Interfaces\ClientInterface;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Interfaces\ScriptInterface;
use AqwSocketClient\Interfaces\SocketInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Messages\XmlMessage;
use AqwSocketClient\Packet;
use AqwSocketClient\Server;
use AqwSocketClient\Sockets\NativeSocket;
use Override;
use RuntimeException;

/**
 * @mago-ignore analyzer:unhandled-thrown-type
 */
final class SocketClient implements ClientInterface
{
    private bool $connected = false;

    public function __construct(
        public readonly Server $server,
        private readonly SocketInterface $socket = new NativeSocket(),
    ) {
        $this->socket->create();
    }

    #[Override]
    public function connect(): void
    {
        if ($this->isConnected()) {
            throw new RuntimeException('Already connected.');
        }

        $this->socket->connect($this->server->hostname, $this->server->port);

        $this->connected = true;
    }

    #[Override]
    public function disconnect(): void
    {
        if (!$this->isConnected()) {
            throw new RuntimeException('Not connected.');
        }

        $this->socket->close();

        $this->connected = false;
    }

    /**
     * @throws RuntimeException When fail to receive data
     * @return MessageInterface[]
     */
    #[Override]
    public function receive(): array
    {
        $this->ensureConnected();

        $buffer = '';

        while (true) {
            ['bytes' => $bytes, 'chunk' => $chunk] = $this->socket->read(1);

            if ($bytes === 0) {
                $this->disconnect();
                break;
            }

            if ($chunk === "\0") {
                break;
            }

            $buffer .= $chunk;
        }

        return array_values(array_filter([
            DelimitedMessage::fromString($buffer),
            JsonMessage::fromString($buffer),
            XmlMessage::fromString($buffer),
        ]));
    }

    /**
     * @throws RuntimeException When not connected or fail to send data
     */
    #[Override]
    public function send(Packet $packet): void
    {
        $this->ensureConnected();

        $data = $packet->unpacketify();
        $length = strlen($data);
        $sent = $this->socket->send($data);

        if ($sent < $length) {
            throw new RuntimeException("Incomplete send: sent {$sent} of {$length} bytes.");
        }
    }

    #[Override]
    public function run(ScriptInterface $script): void
    {
        while ($this->isConnected() && !$script->isDone()) {
            foreach ($this->receive() as $message) {
                $events = [];
                /** @var EventInterface[] $events */

                foreach ($events as $event) {
                    $commands = $script->handle($event);

                    foreach ($commands as $command) {
                        $this->send($command->pack());
                    }
                }
            }
        }
    }

    #[Override]
    public function isConnected(): bool
    {
        return $this->connected;
    }

    private function ensureConnected(): void
    {
        if (!$this->isConnected()) {
            throw new RuntimeException('Not connected.');
        }
    }

    public function __destruct()
    {
        if ($this->isConnected()) {
            $this->socket->close();
        }
    }
}
