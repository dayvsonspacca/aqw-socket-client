<?php

declare(strict_types=1);

namespace AqwSocketClient\Clients;

use AqwSocketClient\Interfaces\ClientInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use AqwSocketClient\Messages\JsonMessage;
use AqwSocketClient\Messages\XmlMessage;
use AqwSocketClient\Packet;
use AqwSocketClient\Server;
use Override;
use RuntimeException;
use Socket;

/**
 * @mago-ignore analyzer:unhandled-thrown-type
 */
final class SocketClient implements ClientInterface
{
    private Socket $socket;
    private bool $connected = false;

    public function __construct(
        public readonly Server $server,
    ) {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            throw new RuntimeException('Failed to create socket: ' . socket_strerror(socket_last_error()));
        }

        $this->socket = $socket;
    }

    #[Override]
    public function connect(): void
    {
        if ($this->isConnected()) {
            throw new RuntimeException('Already connected.');
        }

        // @mago-expect lint:no-error-control-operator
        if (!@socket_connect($this->socket, $this->server->hostname, $this->server->port)) {
            $error = socket_strerror(socket_last_error($this->socket));
            socket_close($this->socket);

            throw new RuntimeException('Failed to connect: ' . $error);
        }

        $this->connected = true;
    }

    #[Override]
    public function disconnect(): void
    {
        if (!$this->isConnected()) {
            throw new RuntimeException('Not connected.');
        }

        socket_close($this->socket);

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
            $chunk = '';
            $bytes = socket_recv($this->socket, $chunk, 1, 0);

            if ($bytes === false) {
                throw new RuntimeException(
                    'Failed to receive data: ' . socket_strerror(socket_last_error($this->socket)),
                );
            }

            if ($bytes === 0) {
                $this->disconnect();
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

        $length = strlen($packet->unpacketify());
        $sent = socket_send($this->socket, $packet->unpacketify(), $length, 0);

        if ($sent === false) {
            throw new RuntimeException('Failed to send data: ' . socket_strerror(socket_last_error($this->socket)));
        }

        if ($sent < $length) {
            throw new RuntimeException("Incomplete send: sent {$sent} of {$length} bytes.");
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
            socket_close($this->socket);
        }
    }
}
