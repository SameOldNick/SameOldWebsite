<?php

namespace App\Listeners\RecentActivity;

use App\Enums\Notifications\ActivityEvent;
use App\Notifications\Activity;
use Illuminate\Auth\Events\Registered;

class LogUserRegistered extends LogActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        $message = __('User ":name" registered an account.', ['name' => $user->getDisplayName()]);
        $context = ['user' => $user->getAuthIdentifier()];

        $this->log(new Activity(ActivityEvent::UserRegistered, $user->created_at ?? now(), $message, $context));
    }
}
