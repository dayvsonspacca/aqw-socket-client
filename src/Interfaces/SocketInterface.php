<?php

declare(strict_types=1);

namespace AqwSocketClient\Interfaces;

use RuntimeException;

/**
 * Abstraction over a raw TCP socket connection.
 * Implement this interface to provide a testable/mockable socket.
 */
interface SocketInterface
{
    /**
     * Creates the underlying socket resource.
     *
     * @throws RuntimeException on failure
     */
    public function create(): void;

    /**
     * Connects to the given host and port.
     *
     * @throws RuntimeException on failure
     */
    public function connect(string $hostname, int $port): void;

    /**
     * Closes the socket.
     */
    public function close(): void;

    /**
     * Reads exactly $length bytes from the socket.
     *
     * @throws RuntimeException on failure
     * @return array{bytes: int, chunk: string}
     */
    public function read(int $length): array;

    /**
     * Sends $data over the socket.
     *
     * @throws RuntimeException on failure
     * @return int bytes actually sent
     */
    public function send(string $data): int;
}
