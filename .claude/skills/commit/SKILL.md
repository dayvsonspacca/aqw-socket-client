---
name: commit
description: Create conventional commits for the aqw-socket-client PHP library. Use this skill whenever the user wants to commit changes, write a commit message, stage files, or run `git commit`. Trigger on phrases like "commit", "fazer commit", "criar commit", "commitar", "save changes to git", or any request to record changes to the repository — even if they don't say "conventional commits" explicitly. Also trigger when the user says things like "salva isso no git" or "cria um commit pra isso".
---

# Commit Skill — aqw-socket-client

This skill helps write clear, consistent conventional commits for the `aqw-socket-client` PHP library.

## Project context

`aqw-socket-client` is a PHP library that implements a socket client for Adventure Quest Worlds (AQW). Its architecture follows a pipeline: raw socket bytes → typed Messages → Events → Scripts → Commands → Packets.

Key source directories under `src/`:
- `Commands/` — actions sent to the AQW server (implement `CommandInterface`)
- `Events/` — typed server-side events (implement `EventInterface`)
- `Scripts/` — logic units that react to events and return commands (extend `AbstractScript`)
- `Objects/` — domain value objects (items, areas, players, monsters, quests…)
- `Messages/` — raw message parsers (XML, JSON, delimited)
- `Sockets/` — socket implementations (`NativeSocket`, `SocketInterface`)
- `Interfaces/` — shared contracts
- `Enums/` — PHP enums used across the codebase
- `Helpers/` — utility classes

Tests live in `tests/Unit/`.

## Conventional commit format

```
<type>(<scope>): <short description>

[optional body]

[optional footer]
```

- The short description is lowercase, imperative mood, no period at the end.
- Keep it under 72 characters.
- Body is optional — add it only when the "why" isn't obvious from the description.

## Types

| Type | When to use |
|---|---|
| `feat` | New feature or new public-facing behavior |
| `fix` | Bug fix |
| `refactor` | Code restructuring with no behavior change |
| `test` | Adding or updating tests |
| `docs` | README, comments, docblocks |
| `chore` | Build scripts, composer, CI, tooling |
| `perf` | Performance improvements |

## Scopes

Scope maps to the part of the codebase being changed. Use the lowercase singular form:

| Scope | When to use |
|---|---|
| `commands` | Changes in `src/Commands/` |
| `events` | Changes in `src/Events/` |
| `scripts` | Changes in `src/Scripts/` |
| `objects` | Changes in `src/Objects/` |
| `messages` | Changes in `src/Messages/` |
| `socket` | Changes in `src/Sockets/` |
| `interfaces` | Changes in `src/Interfaces/` |
| `enums` | Changes in `src/Enums/` |
| `helpers` | Changes in `src/Helpers/` |
| `tests` | Test-only changes |
| `deps` | Dependency updates (composer.json / composer.lock) |

You may also use a **feature/domain scope** when the change is conceptually tied to a gameplay feature rather than a single directory:

| Domain scope | Examples |
|---|---|
| `quests` | Quest-related commands, events, objects |
| `auth` | Login, logout, token handling |
| `area` | Area join, area events |
| `inventory` | Player inventory loading |
| `monsters` | Monster detection events/objects |
| `player` | Player objects and detection |

Omit the scope when the change is truly cross-cutting (multiple unrelated directories at once).

## Workflow

1. Run `git diff --staged` (and `git status`) to understand what's staged.
2. If nothing is staged yet, ask the user which files to include — or stage everything relevant with `git add`.
3. Draft the commit message based on the diff.
4. Show the message to the user and confirm before running `git commit`.
5. Run `git commit -m "..."` using a HEREDOC to preserve formatting.
6. Always add the co-author trailer: `Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>`

## Examples

**Example 1 — new command:**
```
feat(quests): add TurnInQuestCommand
```

**Example 2 — bug fix with body:**
```
fix(socket): prevent premature empty read on EAGAIN

NativeSocket was returning an empty chunk when the OS signaled
EAGAIN, causing the message parser to treat it as EOF.
```

**Example 3 — refactor with domain scope:**
```
refactor(area): re-implement AreaJoinedEvent to use typed objects
```

**Example 4 — test:**
```
test(commands): add unit tests for LoadQuestCommand
```

**Example 5 — chore:**
```
chore: add quality:fix composer script
```

## Things to avoid

- Don't capitalize the description or add a period at the end.
- Don't use past tense ("added", "fixed") — use present imperative ("add", "fix").
- Don't invent scopes not listed above unless genuinely needed.
- Don't include unrelated changes in a single commit — ask the user to split if needed.
- Never skip `--no-verify` unless the user explicitly asks.
- Never amend a published commit without explicit user request.
