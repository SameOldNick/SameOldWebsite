<?php

namespace App\Components\Security\Responders\EventResponder;

use App\Components\Security\Issues\Issue;
use App\Components\Security\Responders\ResponderDriver;

class EventResponder implements ResponderDriver
{
    public function shouldHandle(Issue $issue): bool
    {
        return true;
    }

    public function handle(Issue $issue): void
    {
        Event::dispatch($issue);
    }
}
