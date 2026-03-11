# Script Architecture Redesign

**Date:** 2026-03-11
**Status:** Approved

## Problem

Scripts are purely reactive — `handle()` is only called when an event arrives. This forces every script to depend on a semantically unrelated "startup event" (e.g. `PlayerInventoryLoadedEvent`) just to emit its first command. It also prevents composition: complex flows collapse into monolithic scripts that listen to many unrelated events and manage all sequencing internally.

There are three concrete problems:

1. **No explicit initialization** — scripts cannot emit a command at startup without a fake trigger event.
2. **No clean composition** — multi-step flows (login → join area → load data) live in a single class.
3. **Unsafe multi-command return** — `handle()` returns `CommandInterface[]`, but the AQW server ignores extras when multiple commands arrive too fast, requiring resends.

## Design

### 1. `ScriptInterface` changes

Two targeted changes:

**Add `start(ClientContext): ?CommandInterface`**

Called once by the client when the script begins execution. Returns the first command to send, or `null` if no immediate action is needed. Default implementation in `AbstractScript` returns `null`.

```php
public function start(ClientContext $context): ?CommandInterface;
```

**Change `handle()` to return `?CommandInterface`**

Returning a single optional command enforces one-command-at-a-time, matching the server constraint. The client maintains an internal command queue and drains one command per tick.

```php
public function handle(EventInterface $event, ClientContext $context): ?CommandInterface;
```

`ClientContext` is injected into both methods so scripts can share session state without coupling to each other.

### 2. `ClientContext`

A mutable session-scoped object that lives in the client for the duration of a connection. Scripts read and write named values into it.

```php
final class ClientContext
{
    /** @var MutableMap<string, mixed> */
    private MutableMap $data;

    public function __construct()
    {
        $this->data = new MutableMap([]);
    }

    public function set(string $key, mixed $value): void
    {
        if ($this->data->contains($key)) {
            $this->data->set($key, $value);
        } else {
            $this->data->add($key, $value);
        }
    }

    public function get(string $key): mixed
    {
        return $this->data->get($key);
    }

    public function has(string $key): bool
    {
        return $this->data->contains($key);
    }
}
```

Backed by `Psl\Collection\MutableMap<string, mixed>`. The `mixed` type is confined inside `ClientContext` — scripts that read typed values are responsible for asserting the type themselves (e.g. `Type\instance_of(SocketIdentifier::class)->assert($ctx->get('socket_id'))`).

Scripts share context via well-known string keys. There is no enforced contract on key names — this is a deliberate trade-off favouring simplicity over type-safety.

### 3. `SequenceScript`

Implements `ScriptInterface`. Accepts a list of scripts and runs them in order. Advances to the next script when the current one completes with `ScriptResult::Success`. If any script fails or disconnects, the sequence fails immediately.

On advancement, `SequenceScript` calls `start()` on the next script and returns its initial command, maintaining the one-command-per-tick invariant.

```php
$client->run(new SequenceScript([
    new LoginScript($player, $token),
    new FindMapMonstersScript($area),
    new CollectQuestsScript($questId),
]));
```

Key behaviour:
- `handles()` delegates to the current script — the client only routes events that the active script cares about.
- On failure of a child script, `SequenceScript` calls `failed()` on itself.
- On completion of all scripts, `SequenceScript` calls `success()` on itself.

### 4. `Pipeline` DSL

Implements `ScriptInterface` for linear flows that don't warrant a full class. A `Pipeline` stores an initial command (emitted via `start()`), one expected success event, and zero or more failure events.

```php
Pipeline::start(new JoinAreaCommand($player, $area, new RoomIdentifier(55555)))
    ->waitFor(MonstersDetectedEvent::class)
    ->orFailOn(AreaLockedEvent::class)
    ->orFailOn(AreaMemberOnlyEvent::class)
    ->orFailOn(AreaNotAvaliableEvent::class)
```

When the success event arrives, the pipeline calls `success()`. When any failure event arrives, it calls `failed()`. The matched event is accessible via a public property for the caller to inspect after the sequence ends.

`waitFor()` optionally accepts a callback for side effects or writing to context:

```php
Pipeline::start(new JoinAreaCommand($player, $area, new RoomIdentifier(55555)))
    ->waitFor(AreaJoinedEvent::class, function (AreaJoinedEvent $e, ClientContext $ctx): ?CommandInterface {
        $ctx->set('area_id', $e->area->identifier);
        return null;
    })
    ->orFailOn(AreaLockedEvent::class)
```

`Pipeline` and class-based scripts are interchangeable inside `SequenceScript`:

```php
$client->run(new SequenceScript([
    new LoginScript($player, $token),
    Pipeline::start(new JoinAreaCommand($player, $area, new RoomIdentifier(55555)))
        ->waitFor(MonstersDetectedEvent::class)
        ->orFailOn(AreaLockedEvent::class),
    new CollectQuestsScript($questId),
]));
```

## Migration

Existing scripts need two changes:

1. Replace the "startup event" handler in `handle()` with a `start()` method.
2. Change the return type of `handle()` from `array` to `?CommandInterface` (return the single command or `null`).

For scripts that currently return multiple commands in one `handle()` call, break them into a `SequenceScript` of atomic steps, or queue the additional commands via context if they must come from the same event handler.

## Architecture impact

The client loop changes to:

1. Call `script->start($context)` once and enqueue the result.
2. On each tick: drain one command from the queue and send it.
3. Receive messages, parse events, dispatch to `script->handle($event, $context)`, enqueue the result.
4. Check `script->isDone()` and advance if needed.

The command queue sits inside the client. Scripts remain unaware of queuing — they just return a command or `null`.

## What is not changing

- `ScriptInterface::isDone()`, `result()`, `failed()`, `disconnected()`, `success()` — unchanged.
- `AbstractScript` and `ExpirableScript` — extended, not replaced.
- Events, Commands, Objects, Messages — untouched.
- The overall pipeline: Socket → Message → Event → Script → Command → Packet → Socket.
