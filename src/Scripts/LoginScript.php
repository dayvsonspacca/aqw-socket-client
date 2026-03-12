<?php

declare(strict_types=1);

namespace AqwSocketClient\Scripts;

use AqwSocketClient\Objects\Names\PlayerName;

/**
 * Orchestrates the full login sequence:
 *   1. Establish connection and authenticate.
 *   2. Join the battleon area.
 *   3. Load the player inventory.
 *
 * Equivalent to:
 *   new SequenceScript([
 *       new ConnectAndLoginScript($playerName, $token),
 *       new JoinBattleonScript(),
 *       new LoadInventoryScript(),
 *   ])
 */
final class LoginScript extends SequenceScript
{
    public function __construct(
        PlayerName $playerName,
        #[\SensitiveParameter]
        string $token,
    ) {
        parent::__construct([
            new ConnectAndLoginScript($playerName, $token),
            new JoinBattleonScript(),
            new LoadInventoryScript(),
        ]);
    }
}
