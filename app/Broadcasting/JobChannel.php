<?php

namespace App\Broadcasting;

use App\Models\User;

class JobChannel
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
    public function join(?User $user, string $jobId): array|bool
    {
        if (is_null($user)) {
            return false;
        }

        return $user->privateChannels->active()->lookup($jobId, 'jobs')->isNotEmpty();
    }
}
