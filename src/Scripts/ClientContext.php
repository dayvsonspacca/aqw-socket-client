<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use Psl\Collection\MutableMap;

/**
 * Mutable session-scoped state shared across all scripts in a run.
 *
 * Deliberately not a value object — it is mutable by design and does not
 * belong in src/Objects/. Each client run creates one instance and passes
 * it through every start() and handle() call.
 */
final class ClientContext
{
    /** @var MutableMap<string, mixed> */
    private MutableMap $data;

    public function __construct()
    {
        /** @var array<string, mixed> $empty */
        $empty = [];
        $this->data = new MutableMap($empty);
    }

    public function set(string $key, mixed $value): void
    {
        if ($this->data->contains($key)) {
            $this->data->set($key, $value);
            return;
        }

        $this->data->add($key, $value);
    }

    public function get(string $key): mixed
    {
        if (!$this->data->contains($key)) {
            return null;
        }

        return $this->data->get($key);
    }

    public function has(string $key): bool
    {
        return $this->data->contains($key);
    }
}
