<?php

declare(strict_types=1);

namespace AqwSocketClient;

/**
 * Simulates a AQW client communication with server
 */
class Client
{
    private $socket = null;

    public function __construct(private readonly Server $server) {}

    public function connect()
    {
        $this->socket = @stream_socket_client(
            "tcp://{$this->server->hostname}:{$this->server->port}",
            $errno,
            $errstr,
            5
        );

        if (!$this->socket) {
            echo "Erro: $errstr ($errno)\n";
            return;
        }

        stream_set_blocking($this->socket, false);

        while (!feof($this->socket)) {
            $data = fgets($this->socket, 1024);
            if ($data) {
                $this->onData($data);
            }
            usleep(100000);
        }

        fclose($this->socket);
    }

    private function onData(string $mensagem)
    {
        echo "[RAW] $mensagem\n";
    }
}
