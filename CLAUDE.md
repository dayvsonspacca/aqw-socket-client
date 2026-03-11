# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
composer test                  # run all unit tests
composer test:unit             # run unit tests with --testdox
composer test:coverage         # run tests + generate HTML coverage in public/

./vendor/bin/phpunit --filter TestClassName          # run a single test class
./vendor/bin/phpunit --filter it_can_create          # run a single test method
./vendor/bin/phpunit tests/Unit/Events/QuestLoadedEventTest.php  # run a specific file

composer lint                  # lint (mago)
composer lint:fix              # lint + auto-fix
composer analyze               # static analysis (mago)
composer fmt                   # format code
composer fmt:check             # check formatting without changing files

composer quality               # lint + analyze + fmt:check + test
composer quality:fix           # lint:fix + fmt (auto-fix everything)
```

## Rules

- After writing or modifying code, always run `composer quality` (lint + analyze + fmt:check + test) instead of just `composer test`. If lint or analyze fails, fix the issues before proceeding.
- If formatting fails, run `composer quality:fix` to auto-fix, then re-run `composer quality` to confirm.

## Architecture

The pipeline is: **Socket → Message → Event → Script → Command → Packet → Socket**

Raw bytes come in from the AQW server, get parsed into a typed `MessageInterface`, matched to an `EventInterface`, dispatched to a `ScriptInterface`, which returns `CommandInterface[]` that are packed and sent back.

### Messages (`src/Messages/`)

Three message formats exist:
- `XmlMessage` — cross-domain policy on connect
- `DelimitedMessage` — `%xt%cmd%-1%...%` format (login response, warnings)
- `JsonMessage` — `{"t":"xt","b":{"r":...,"o":{"cmd":"..."}}}` format (most server responses)

`JsonMessage::from()` extracts `$b['o']` as `$message->data` and resolves `cmd` to a `JsonMessageType` enum. **`$message->data` is already the `o` object** — do not re-navigate `['o']` inside events.

### Events (`src/Events/`)

Each event implements `EventInterface::from(MessageInterface): ?EventInterface`. Return `null` if the message doesn't match. Events are tried in order until one matches.

Events parse raw arrays from `$message->data` and construct typed value objects directly inside `from()`. The `@mago-ignore analyzer:mixed-argument,mixed-array-access,mixed-assignment,less-specific-nested-argument-type` annotation is used on `from()` when accessing untyped JSON arrays.

### Scripts (`src/Scripts/`)

Scripts extend `AbstractScript` (or `ExpirableScript` for time-bounded operations). They declare which event classes they handle via `handles(): array` and react in `handle(EventInterface): CommandInterface[]`.

Completion is signaled by calling `success()`, `failed()`, or `disconnected()` — all of which set `isDone() = true`. `ExpirableScript` adds `isExpired()` support (defaults to 1-minute timeout).

### Commands (`src/Commands/`)

Commands implement `CommandInterface::pack(): Packet`. Most use `Packet::packetify()` with the `%xt%zm%cmd%...%` format. Each command carries its own typed value objects and serializes them in `pack()`.

### Objects (`src/Objects/`)

Value objects are `final readonly class`, immutable, validated inline in the constructor using PSL:
- `Type\positive_int()->assert($v)` — throws `AssertException`
- `Psl\invariant($cond, $msg, ...$args)` — throws `InvariantViolationException`

Base hierarchies: `Identifier` (positive int), `Name` (non-empty string), `Level` (positive int with optional cap) — all in their respective subdirectories. Leaf classes extend these with no added logic.

The `QuestRewardInterface` hierarchy: `ExperienceReward`, `GoldReward`, `ReputationReward`, `ItemReward` — all implement it for use as `list<QuestRewardInterface>` in `Quest`.

### Test helpers

`MessageGenerator` (`src/Helpers/MessageGenerator.php`) provides pre-built raw server message strings for use in event tests. Add a static method here whenever a new event needs a test fixture.

### Validation in tests

- `\Psl\Type\Exception\AssertException` — for `Type\*->assert()` failures
- `\Psl\Exception\InvariantViolationException` — for `Psl\invariant()` failures
