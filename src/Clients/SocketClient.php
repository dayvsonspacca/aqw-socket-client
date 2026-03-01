<?php

declare(strict_types=1);

namespace AqwSocketClient\Clients;

use AqwSocketClient\Interfaces\MessageInterface;
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
final class SocketClient extends AbstractClient
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
                break;
            }

            if ($chunk === "\0") {
                break;
            }

            $buffer .= $chunk;
        }

        return array_values(array_filter([
            DelimitedMessage::from($buffer),
            JsonMessage::from($buffer),
            XmlMessage::from($buffer),
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
        $this->socket->send($data);
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
