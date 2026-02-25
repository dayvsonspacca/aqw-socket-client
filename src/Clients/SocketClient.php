<?php

declare(strict_types=1);

namespace AqwSocketClient\Clients;

use AqwSocketClient\Interfaces\ClientInterface;
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

    #[Override]
    public function isConnected(): bool
    {
        return $this->connected;
    }

    public function __destruct()
    {
        if ($this->isConnected()) {
            socket_close($this->socket);
        }
    }
}
