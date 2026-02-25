<?php

declare(strict_types=1);

namespace AqwSocketClient\Clients;

use AqwSocketClient\Configuration;
use AqwSocketClient\Interfaces\ClientInterface;
use RuntimeException;
use Socket;

final class SocketClient implements ClientInterface
{
    private ?Socket $socket = null;

    public function __construct(
        public readonly Configuration $configuration,
    ) {}

    public function connect(): void
    {
        if ($this->isConnected()) {
            throw new RuntimeException('Already connected.');
        }

        $server = $this->configuration->server;

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            throw new RuntimeException('Failed to create socket: ' . socket_strerror(socket_last_error()));
        }

        // @mago-expect lint:no-error-control-operator
        if (!@socket_connect($socket, $server->hostname, $server->port)) {
            $error = socket_strerror(socket_last_error($socket));
            socket_close($socket);

            throw new RuntimeException('Failed to connect: ' . $error);
        }

        $this->socket = $socket;
    }

    public function disconnect(): void
    {
        if (!$this->isConnected()) {
            throw new RuntimeException('Not connected.');
        }

        socket_close($this->socket);

        $this->socket = null;
    }

    public function isConnected(): bool
    {
        return $this->socket !== null;
    }

    public function __destruct()
    {
        if ($this->isConnected()) {
            socket_close($this->socket);
        }
    }
}
