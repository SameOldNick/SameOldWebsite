<?php

namespace App\Broadcasting;

use App\Models\User;

class ProcessChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(?User $user, $processId): array|bool
    {
        if (is_null($user)) {
            return false;
        }

        return $user->privateChannels->active()->lookup($processId, 'processes')->isNotEmpty();
    }
}
