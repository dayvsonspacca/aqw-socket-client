<?php

declare(strict_types=1);

namespace AqwSocketClient\Events;

use AqwSocketClient\Enums\DelimitedMessageType;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\MessageInterface;
use AqwSocketClient\Messages\DelimitedMessage;
use Override;
use Psl\Str;

/**
 * Represents an event triggered after the client try to join a area
 */
final class AreaMemberOnlyEvent implements EventInterface
{
    /**
     * @return ?AreaMemberOnlyEvent
     */
    #[Override]
    public static function from(MessageInterface $message): ?EventInterface
    {
        if ($message instanceof DelimitedMessage && $message->type === DelimitedMessageType::Warning) {
            if (Str\contains($message->data[0], 'is an Membership-Only Map.')) {
                return new self();
            }
        }

        return null;
    }
}
