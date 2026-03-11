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

`start()` is added to `ScriptInterface` as a required method. `AbstractScript` provides the default implementation, which returns `null`. Concrete scripts override it only when they need to emit an initial command.

Called once by the client when the script begins execution. Returns the first command to send, or `null` if no immediate action is needed.

```php
// ScriptInterface
public function start(ClientContext $context): ?CommandInterface;

// AbstractScript default
public function start(ClientContext $context): ?CommandInterface
{
    return null;
}
```

**Change `handle()` to return `?CommandInterface`**

Returning a single optional command enforces one-command-at-a-time, matching the server constraint. The client maintains an internal command queue and drains one command per tick.

```php
public function handle(EventInterface $event, ClientContext $context): ?CommandInterface;
```

`ClientContext` is injected into both methods so scripts can share session state without coupling to each other.

### 2. `ClientContext`

`ClientContext` lives in `src/Scripts/ClientContext.php`. It is a deliberate exception to the project's `final readonly class` rule: it is mutable by design and is not a value object. It does not belong in `src/Objects/`.

A mutable session-scoped object created once by the client before calling `script->start()`, then passed through every `start()` and `handle()` call for the entire run. This is what enables cross-script communication in `SequenceScript` — a script running early in the sequence writes a value, and a script running later reads it.

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

Implements `ScriptInterface`. Accepts a list of scripts and runs them in order. Advances to the next script when the current one completes with `ScriptResult::Success`. If any script fails, disconnects, or expires, the sequence fails immediately.

On advancement, `SequenceScript::handle()` calls `start()` on the next script and returns its initial command (or `null` if the next script has no initial command), maintaining the one-command-per-tick invariant. The returned value goes directly into the client queue.

`SequenceScript::isDone()` only returns `true` once the entire sequence completes or fails — intermediate advancement between child scripts is handled internally and is opaque to the client. The client does not need to implement any advancement logic of its own.

```php
$client->run(new SequenceScript([
    new LoginScript($player, $token),
    new FindMapMonstersScript($area),
    new CollectQuestsScript($questId),
]));
```

Key behaviour:
- `handles()` delegates to the current child script — the client only routes events that the active script cares about. Events not declared by the active child are ignored, as they are in the existing routing design.
- When a child completes with `ScriptResult::Success`, `SequenceScript` advances to the next child and calls its `start()`.
- When a child completes with `ScriptResult::Failed`, `ScriptResult::Disconnected`, or `ScriptResult::Expired`, `SequenceScript` calls `failed()` on itself.
- When the last child completes with `ScriptResult::Success`, `SequenceScript` calls `success()` on itself.
- **Expiry of child scripts:** `SequenceScript` does not implement `ExpirableScriptInterface`. Instead, at the start of each `handle()` call, `SequenceScript` checks whether the active child implements `ExpirableScriptInterface` and whether `isExpired()` returns true. If so, it calls `$child->expired()` and immediately calls `failed()` on itself. The client-level expiry check (step 3 in the architecture loop) applies only to the top-level script passed to `run()` — if the top-level script is a `SequenceScript`, the client loop never sees the children directly, and child expiry is handled internally by `SequenceScript`.

### 4. `Pipeline` DSL

Implements `ScriptInterface` for linear flows that don't warrant a full class. A `Pipeline` stores an initial command (emitted via `ScriptInterface::start()`), one expected success event class, and zero or more failure event classes.

The static factory method is named `Pipeline::send()` (not `start()`) to avoid naming confusion with the `ScriptInterface::start()` instance method that both carry.

`Pipeline` implements `handles()` by returning the union of the `waitFor` event class and all `orFailOn` event classes. `Pipeline::handle()` matches the incoming event against its registered classes: if it matches the success class, it invokes the optional callback and calls `success()`; if it matches a failure class, it calls `failed()`.

```php
Pipeline::send(new JoinAreaCommand($player, $area, new RoomIdentifier(55555)))
    ->waitFor(MonstersDetectedEvent::class)
    ->orFailOn(AreaLockedEvent::class)
    ->orFailOn(AreaMemberOnlyEvent::class)
    ->orFailOn(AreaNotAvailableEvent::class)
```

When the success event arrives, the pipeline calls `success()`. When any failure event arrives, it calls `failed()`. The matched event — whether success or failure — is stored in a public `?EventInterface $matchedEvent` property, accessible after the pipeline completes.

`orFailOn()` does not accept a callback. Failure paths are terminal and carry no follow-up command. If the caller needs to inspect the failure reason, it reads `$matchedEvent` after the sequence ends.

`waitFor()` optionally accepts a callback for side effects, writing to context, or emitting one additional command. If the callback returns a non-null command, `Pipeline::handle()` returns it — it enters the client queue and is sent on the next available tick, like any other command:

```php
Pipeline::send(new JoinAreaCommand($player, $area, new RoomIdentifier(55555)))
    ->waitFor(AreaJoinedEvent::class, function (AreaJoinedEvent $e, ClientContext $ctx): ?CommandInterface {
        $ctx->set('area_id', $e->area->identifier);
        return null; // no follow-up command needed
    })
    ->orFailOn(AreaLockedEvent::class)
```

`Pipeline` and class-based scripts are interchangeable inside `SequenceScript`:

```php
$client->run(new SequenceScript([
    new LoginScript($player, $token),
    Pipeline::send(new JoinAreaCommand($player, $area, new RoomIdentifier(55555)))
        ->waitFor(MonstersDetectedEvent::class)
        ->orFailOn(AreaLockedEvent::class),
    new CollectQuestsScript($questId),
]));
```

## Migration

Existing scripts need two changes:

1. Replace the "startup event" handler in `handle()` with a `start()` method.
2. Change the return type of `handle()` from `array` to `?CommandInterface` (return the single command or `null`).

**Rule:** a script must not return a command from `handle()` on the same tick it calls `success()` or `failed()`. Completion and command emission are mutually exclusive in a single call.

`LoginScript` is the main existing script that needs migration. It currently handles `ConnectionEstablishedEvent`, `LoginRespondedEvent`, and `AreaJoinedEvent` as one monolith. Under the new design it can be decomposed into a `SequenceScript` of smaller atomic scripts, each handling one step, with `ClientContext` carrying `socketId` and `areaId` between them.

For any script that currently returns multiple commands from a single `handle()` call: break the flow into a `SequenceScript` of atomic steps, one command per step.

## Architecture impact

The client loop changes to:

1. Create a `ClientContext` instance.
2. Call `script->start($context)` once and enqueue the result if non-null.
3. On each tick: check if the script is an `ExpirableScriptInterface` and call `expired()` if `isExpired()` returns true (unchanged behaviour).
4. If the command queue is non-empty, drain one command and send it.
5. Receive messages, parse events, dispatch to `script->handle($event, $context)`, enqueue the result if non-null.
6. Check `script->isDone()`. When true, the run is complete — no client-side advancement logic is needed beyond this check.

`start()` and `handle()` both feed the same single command queue inside the client. There is no distinction between the initial command and event-triggered commands — both are drained one per tick in the same loop. Scripts never interact with the queue directly.

When the top-level script is a `SequenceScript`, step 6 only triggers once the entire sequence has finished; all intermediate child advancement happens inside `SequenceScript::handle()`.

## What is not changing

- `ScriptInterface::isDone()`, `result()`, `failed()`, `disconnected()`, `success()` — unchanged.
- `AbstractScript` and `ExpirableScript` — extended, not replaced.
- Events, Commands, Objects, Messages — untouched.
- The overall pipeline: Socket → Message → Event → Script → Command → Packet → Socket.
