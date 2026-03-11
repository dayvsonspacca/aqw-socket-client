---
name: aqw-object-creator
description: >
  Creates PHP value objects and their PHPUnit tests for the aqw-socket-client project,
  following all project conventions and using PSL (PHP Standard Library) for validation.
  Use this skill whenever the user asks to create a new class, object, value object, DTO,
  or any PHP class in the src/Objects/ directory. Also trigger for phrases like
  "cria um objeto", "cria uma classe", "novo objeto", "new object", "add a class",
  "preciso de um objeto", or any request to add a new PHP type to the project — even
  if the user doesn't mention "PSL" or "objects" explicitly. Always use this skill
  before creating any PHP file in this project.
---

# AQW Object Creator

Creates PHP value objects for the `aqw-socket-client` project following established
conventions and using PSL (PHP Standard Library) for all validation and utilities.

## Project Layout

```
src/Objects/              → implementation files
  Identifiers/            → classes extending Identifier
  Names/                  → classes extending Name
  Levels/                 → classes extending Level
tests/Unit/Objects/       → test files (mirrors src/Objects/ structure)
```

- Namespace: `AqwSocketClient\Objects` (adjust for subdirectories)
- Test namespace: `AqwSocketClient\Tests\Unit\Objects`

---

## Object Anatomy

Every object must follow this exact template:

```php
<?php

declare(strict_types=1);

namespace AqwSocketClient\Objects;

use Psl;
use Psl\Type;

final readonly class ClassName
{
    public function __construct(
        public readonly TypeHint $property,
        public readonly AnotherType $other,
    ) {
        Type\non_empty_string()->assert($this->property);
        Psl\invariant($this->other > 0, 'Other must be positive, got %d.', $this->other);
    }
}
```

**Non-negotiable rules:**
- `declare(strict_types=1)` always present
- `final class` unless it's an abstract base meant for subclassing
- All properties `public readonly` with promoted constructor syntax
- Trailing comma after the last constructor parameter
- No setters — objects are immutable by design
- **Never** use raw `if + throw new InvalidArgumentException` — always use PSL

---

## PSL Usage Guide

### Type Validation (`Psl\Type`)

The core module for input validation. Use `->assert()` for strict checking:

| Scenario | PSL call |
|----------|----------|
| Non-empty string | `Type\non_empty_string()->assert($v)` |
| Any string | `Type\string()->assert($v)` |
| Integer | `Type\int()->assert($v)` |
| Positive integer (> 0) | `Type\positive_int()->assert($v)` |
| Integer in range | `Type\u8()->assert($v)` (0–255), `Type\i16()`, etc. |
| Float | `Type\float()->assert($v)` |
| Boolean | `Type\bool()->assert($v)` |
| Nullable | `Type\nullable(Type\string())->assert($v)` |
| Typed list | `Type\vec(Type\int())->assert($v)` |
| Non-empty list | `Type\non_empty_vec(Type\string())->assert($v)` |
| Typed map | `Type\dict(Type\string(), Type\int())->assert($v)` |

**Three assertion modes — choose intentionally:**
- `->assert($v)` — strict check, throws `AssertException` if it fails
- `->coerce($v)` — tries to convert (e.g., `"42"` → `42`), throws `CoercionException`
- `->matches($v)` — returns `bool`, never throws

For business-rule constraints (not just type checks), prefer `Psl\invariant`:

```php
use Psl;

Psl\invariant($value >= 0, 'Value must be non-negative, got %d.', $value);
// throws Psl\Exception\InvariantViolationException on failure
```

### String Operations (`Psl\Str`)

Always use instead of native PHP string functions — PSL is Unicode-aware by default:

```php
use Psl\Str;

Str\length($str)           // instead of strlen() — counts codepoints
Str\uppercase($str)        // instead of strtoupper()
Str\lowercase($str)        // instead of strtolower()
Str\contains($str, $sub)   // instead of str_contains()
Str\starts_with($str, $p)  // instead of str_starts_with()
Str\slice($str, $start)    // instead of substr()
Str\Byte\length($str)      // raw byte length when needed
```

### Collections (`Psl\Vec`, `Psl\Dict`)

Use when objects contain arrays or lists:

```php
use Psl\Vec;
use Psl\Dict;

// Lists (re-indexes keys)
Vec\map($list, fn($x) => $x * 2)          // instead of array_map()
Vec\filter($list, fn($x) => $x > 0)       // instead of array_filter()
Vec\values($list)                           // instead of array_values()

// Associative arrays (preserves keys)
Dict\map($dict, fn($v) => strtoupper($v))  // transform values
Dict\filter($dict, fn($v) => $v > 0)       // filter entries
Dict\merge($a, $b)                          // instead of array_merge()
```

### Option & Result Types

Use when absence or failure are meaningful domain concepts:

```php
use Psl\Option\Option;
use Psl\Result;

// Option — replaces nullable returns
Option::some($value)
Option::none()
Option::from_nullable($nullableValue)

// Result — wraps operations that can fail without throwing
$result = Result\wrap(fn() => riskyOperation());
$result->map(fn($v) => transform($v));
```

### PSL Import Conventions

```php
use Psl;               // Psl\invariant(), Psl\Ref
use Psl\Type;          // Type\non_empty_string(), Type\int(), etc.
use Psl\Str;           // String operations
use Psl\Vec;           // List operations
use Psl\Dict;          // Map operations
use Psl\Option\Option; // Optional values
use Psl\Result;        // Result types
```

---

## Object Categories

### 1. DTO (Data Transfer Object)
Composes other typed objects — no primitive validation needed:

```php
final readonly class Area
{
    public function __construct(
        public readonly AreaIdentifier $identifier,
        public readonly AreaName $name,
        public readonly RoomIdentifier $room,
    ) {}
}
```

### 2. Validated Primitive
Wraps a primitive with a business-rule constraint:

```php
final readonly class Health
{
    public function __construct(
        public readonly int $value,
    ) {
        Psl\invariant($this->value >= 0, 'Health must be non-negative, got %d.', $this->value);
    }
}
```

### 3. Abstract Base (for sealed hierarchies)
Used for Identifier / Name / Level patterns:

```php
abstract class Name implements Stringable
{
    public function __construct(
        public readonly string $value,
    ) {
        Type\non_empty_string()->assert($this->value);
    }

    #[Override]
    public function __toString(): string
    {
        return $this->value;
    }
}
```

### 4. Subclass of a Base (sealed leaf)
Adds no logic — just provides a distinct type:

```php
final readonly class QuestName extends Name {}
```

---

## Test Conventions

```php
<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\ClassName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ClassNameTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $obj = new ClassName(/* valid args */);

        $this->assertInstanceOf(ClassName::class, $obj);
        $this->assertSame($expected, $obj->property);
    }

    #[Test]
    public function should_throw_exception_when_[condition](): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new ClassName(/* invalid args */);
    }
}
```

**Test rules:**
- One test file per class, placed at `tests/Unit/Objects/[Subdir/]ClassNameTest.php`
- Always test the happy path (`it_can_create`) and every validation rule
- For abstract classes, use anonymous subclasses: `new class($arg) extends AbstractBase {}`
- Expect `\Psl\Type\Exception\AssertException` for `Type\*->assert()` failures
- Expect `\Psl\Exception\InvariantViolationException` for `Psl\invariant()` failures
- Expect `\Psl\Type\Exception\CoercionException` for `Type\*->coerce()` failures

---

## Step-by-Step Process

1. **Understand the object** — clarify properties, types, and validation rules with the user if needed
2. **Determine the category** — DTO, validated primitive, abstract base, or sealed subclass
3. **Determine the location** — `src/Objects/` or the appropriate subdirectory
4. **Write the implementation** at `src/Objects/[Subdir/]ClassName.php`
5. **Write the test** at `tests/Unit/Objects/[Subdir/]ClassNameTest.php`
6. **Verify PSL is used** for all validation — no raw `if + throw` patterns
