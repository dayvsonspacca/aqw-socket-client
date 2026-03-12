<?php

declare(strict_types=1);

namespace AqwSocketClient\Tests\Unit\Objects;

use AqwSocketClient\Objects\Quest\QuestDescription;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class QuestDescriptionTest extends TestCase
{
    #[Test]
    public function it_can_create(): void
    {
        $description = new QuestDescription(
            'Bring me some Mana Energy from the Mana Golem.',
            'Wonderful! Go ahead and spin the wheel!',
        );

        $this->assertInstanceOf(QuestDescription::class, $description);
        $this->assertSame($description->text, 'Bring me some Mana Energy from the Mana Golem.');
        $this->assertSame($description->completionText, 'Wonderful! Go ahead and spin the wheel!');
    }

    #[Test]
    public function should_throw_exception_when_text_empty(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new QuestDescription('', 'Completion text.');
    }

    #[Test]
    public function it_can_create_without_completion_text(): void
    {
        $description = new QuestDescription('Bring me some Mana Energy from the Mana Golem.');

        $this->assertInstanceOf(QuestDescription::class, $description);
        $this->assertNull($description->completionText);
    }

    #[Test]
    public function should_throw_exception_when_completion_text_empty(): void
    {
        $this->expectException(\Psl\Type\Exception\AssertException::class);

        new QuestDescription('Description.', '');
    }
}
