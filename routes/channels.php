<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('jobs.{jobId}', function (?User $user, $jobId) {
    if (is_null($user)) {
        return false;
    }

    return $user->privateChannels->active()->lookup($jobId, 'jobs')->isNotEmpty();
});

Broadcast::channel('processes.{processId}', function (?User $user, $processId) {
    if (is_null($user)) {
        return false;
    }

    return $user->privateChannels->active()->lookup($processId, 'processes')->isNotEmpty();
});
