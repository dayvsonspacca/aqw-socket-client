# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### Running Tests

```bash
composer test                  # run all unit tests
composer test:unit             # run unit tests with --testdox
composer test:coverage         # run tests + generate HTML coverage in public/

./vendor/bin/phpunit --filter TestClassName                              # run a single test class
./vendor/bin/phpunit --filter it_can_create                             # run a single test method
./vendor/bin/phpunit tests/Unit/Events/QuestLoadedEventTest.php         # run a specific file
```

Run `./vendor/bin/phpunit --help` to discover more filter and output options.

### Code Quality

Code quality is managed by **mago** — a PHP linter, static analyzer, and formatter. The composer scripts are the main entry points:

```bash
composer lint                  # lint (mago)
composer lint:fix              # lint + auto-fix
composer analyze               # static analysis (mago)
composer fmt                   # format code
composer fmt:check             # check formatting without changing files

composer quality               # lint + analyze + fmt:check + test
composer quality:fix           # lint:fix + fmt (auto-fix everything)
```

All mago subcommands accept `[PATH]...` to scope to specific files or directories:

```bash
./vendor/bin/mago lint src/Events/QuestLoadedEvent.php
./vendor/bin/mago analyze src/Objects/Quest/
./vendor/bin/mago format src/Events/QuestLoadedEvent.php
```

Run `./vendor/bin/mago lint --help`, `./vendor/bin/mago analyze --help`, or `./vendor/bin/mago format --help` to discover more options (e.g. `--staged`, `--fix`, `--only`).

- After writing or modifying code, run `composer quality` (lint + analyze + fmt:check + test). If lint or analyze fails, fix the issues before proceeding.
- If formatting fails, run `composer quality:fix` to auto-fix, then re-run `composer quality` to confirm.

## Code Standards

### PSL

Use PSL (PHP Standard Library) to its maximum — prefer PSL over native PHP whenever a typed, safe equivalent exists:

- **Validation:** `Type\positive_int()->assert($v)` for positive integers; `Psl\invariant($cond, $msg)` for business rules
- **Collections:** `Vec\`, `Dict\`, `Iter\` instead of `array_*` functions
- **Strings:** `Str\` instead of `str_*`, `strlen`, `strpos`, etc.

### Objects

- Always `final readonly class` — immutable, no setters
- Validate in the constructor only; no business logic beyond validation
- Use `Type\positive_int()->assert()` for IDs and levels; `Psl\invariant()` for domain rules

### Events

- Implement `EventInterface::from(MessageInterface): ?EventInterface` — return `null` if the message doesn't match
- Use `@mago-ignore analyzer:mixed-argument,mixed-array-access,mixed-assignment,less-specific-nested-argument-type` only on `from()` when accessing untyped JSON arrays

### Commands

- Implement `CommandInterface::pack(): Packet`
- Use `Packet::packetify()` with the `%xt%zm%cmd%...%` format
- Carry typed value objects and serialize them in `pack()`

### PHP

- Every file must have `declare(strict_types=1)`
- Explicit types on all parameters, return types, and properties — avoid `mixed` except where truly unavoidable
- Prefer early returns to reduce nesting and improve readability
- Avoid unnecessary `null` — use typed objects instead

### Tests

- Always use the `#[Test]` attribute on test methods
- Method name must describe the behavior being tested — use any descriptive prefix (`it_`, `should_`, `test_if_`, etc.) in snake_case
- One behavior per test method

## Workflow

When adding support for a new server command or event, follow this order:

1. **Create value objects** in `src/Objects/` under the appropriate domain subdirectory
2. **Create the Event** in `src/Events/` implementing `EventInterface`
3. **Add a fixture** to `MessageGenerator` (`src/Helpers/MessageGenerator.php`) for the new event
4. **Create the Command** in `src/Commands/` (if needed) implementing `CommandInterface`
5. **Write tests** mirroring the structure in `tests/Unit/`
6. **Validate each file individually** before running the full suite:
   ```bash
   ./vendor/bin/mago lint src/Events/NewEvent.php
   ./vendor/bin/mago analyze src/Events/NewEvent.php
   ./vendor/bin/mago format src/Events/NewEvent.php
   ./vendor/bin/phpunit --filter NewEventTest
   ```
7. **Run `composer quality`** to confirm nothing broke in the rest of the project

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

Organized by domain:
- `Identifiers/` — positive-int IDs (`Identifier` base). `Names/` — non-empty string names (`Name` base). `Levels/` — positive-int levels with optional cap (`Level` base). Leaf classes extend these with no added logic.
- `Quest/` — `Quest`, `QuestDescription`, `QuestTurnInItem`, rewards (`QuestRewardInterface`: `ExperienceReward`, `GoldReward`, `ReputationReward`, `ItemReward`), requirements (`QuestRequirementInterface`: `LevelRequirement`, `ReputationRequirement`, `ClassRankRequirement`, `ItemRequirement`, `QuestRequirement`).
- `Monster/` — `Monster`, `Health`.
- `Area/` — `Area`.
- Root — shared objects: `Faction`, `GameFileMetadata`.

### Test Helpers

`MessageGenerator` (`src/Helpers/MessageGenerator.php`) provides pre-built raw server message strings for use in event tests. Add a static method here whenever a new event needs a test fixture.

### Validation in Tests

- `\Psl\Type\Exception\AssertException` — for `Type\*->assert()` failures
- `\Psl\Exception\InvariantViolationException` — for `Psl\invariant()` failures
