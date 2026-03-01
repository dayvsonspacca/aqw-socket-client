<?php

declare(strict_types=1);

namespace AqwSocketClient\Sockets;

use AqwSocketClient\Interfaces\SocketInterface;
use Override;
use RuntimeException;
use Socket;

/**
 * Concrete socket implementation using PHP's native socket_* functions.
 * @mago-ignore analyzer:possibly-null-argument
 */
final class NativeSocket implements SocketInterface
{
    private ?Socket $socket = null;

    #[Override]
    public function create(): void
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        // @codeCoverageIgnoreStart
        if ($socket === false) {
            throw new RuntimeException('Failed to create socket: ' . socket_strerror(socket_last_error()));
        }
        // @codeCoverageIgnoreEnd

        $this->socket = $socket;
    }

    #[Override]
    public function connect(string $hostname, int $port): void
    {
        $this->assertCreated();

        // @mago-expect lint:no-error-control-operator
        if (!@socket_connect($this->socket, $hostname, $port)) {
            $error = socket_strerror(socket_last_error($this->socket));
            socket_close($this->socket);
            $this->socket = null;

            throw new RuntimeException('Failed to connect: ' . $error);
        }
    }

    #[Override]
    public function close(): void
    {
        if ($this->socket !== null) {
            socket_close($this->socket);
            $this->socket = null;
        }
    }

    #[Override]
    public function read(int $length): array
    {
        $this->assertCreated();

        $chunk = '';
        $bytes = socket_recv($this->socket, $chunk, $length, MSG_DONTWAIT);

        // @codeCoverageIgnoreStart
        if ($bytes === false) {
            $error = socket_last_error($this->socket);

            if (in_array($error, [SOCKET_EAGAIN, SOCKET_EWOULDBLOCK], true)) {
                return ['bytes' => 0, 'chunk' => ''];
            }

            throw new RuntimeException('Failed to receive data: ' . socket_strerror($error));
        }
        // @codeCoverageIgnoreEnd

        return ['bytes' => $bytes, 'chunk' => $chunk];
    }

    #[Override]
    public function send(string $data): int
    {
        $this->assertCreated();

        $length = strlen($data);
        $sent = socket_send($this->socket, $data, $length, 0);

        // @codeCoverageIgnoreStart
        if ($sent === false) {
            throw new RuntimeException('Failed to send data: ' . socket_strerror(socket_last_error($this->socket)));
        }
        // @codeCoverageIgnoreEnd

        return $sent;
    }

    public function __destruct()
    {
        $this->close();
    }

    private function assertCreated(): void
    {
        if ($this->socket === null) {
            throw new RuntimeException('Socket has not been created yet.');
        }
    }
}
