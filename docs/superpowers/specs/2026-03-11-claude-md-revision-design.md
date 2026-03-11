# Design: CLAUDE.md Revision

**Date:** 2026-03-11
**Status:** Approved

## Goal

Rewrite `CLAUDE.md` to be more specific and prescriptive, organized into clear sections so Claude Code can quickly locate the right rule for any given context.

## Structure

The file is reorganized from a flat layout into five H2 sections, each with H3 subsections:

```
## Commands
  ### Running Tests
  ### Code Quality
## Code Standards
  ### PSL
  ### Objects
  ### Events
  ### Commands
  ### PHP
  ### Tests
## Workflow
## Architecture
  ### Messages
  ### Events
  ### Scripts
  ### Commands
  ### Objects
  ### Test Helpers
  ### Validation in Tests
```

## Section Decisions

### Commands

- Split into `### Running Tests` and `### Code Quality`
- Explicitly name **mago** as the tool behind lint/analyze/format
- Document that all mago subcommands accept `[PATH]...` to scope to specific files/directories, with examples
- Point to `--help` on each subcommand to discover further options
- Keep the two quality rules (run `composer quality` after changes; use `composer quality:fix` to auto-fix formatting)

### Code Standards

- **PSL:** Use PSL to the maximum — prefer PSL over native PHP for collections (`Vec\`, `Dict\`, `Iter\`), strings (`Str\`), and validation (`Type\*`, `Psl\invariant`)
- **Objects:** `final readonly class`, validated in constructor only, no logic beyond validation
- **Events:** `from()` returns `null` on mismatch; `$message->data` is already the `o` object; `@mago-ignore` scoped to `from()`
- **Commands:** `pack(): Packet` via `Packet::packetify()`; typed value objects serialized in `pack()`
- **PHP:** `declare(strict_types=1)` everywhere; explicit types on all parameters/returns/properties; early returns to reduce nesting; avoid unnecessary `null`
- **Tests:** `#[Test]` attribute required; method names describe behavior with any descriptive prefix (`it_`, `should_`, `test_if_`, etc.) in snake_case; one behavior per test

### Workflow

Sequential steps for adding a new server command/event:

1. Create value objects in `src/Objects/`
2. Create the Event in `src/Events/`
3. Add fixture to `MessageGenerator`
4. Create the Command in `src/Commands/` (if needed)
5. Write tests in `tests/Unit/`
6. Validate each file individually with mago + phpunit --filter
7. Run `composer quality` for full suite confirmation

### Architecture

No structural changes — content preserved from current CLAUDE.md verbatim.
