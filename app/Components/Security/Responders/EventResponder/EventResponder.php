<?php

namespace App\Components\Security\Responders\EventResponder;

use App\Components\Security\Responders\ResponderDriver;
use App\Components\Security\Issues\Issue;

class EventResponder implements ResponderDriver
{
    public function shouldHandle(Issue $issue): bool {
        return true;
    }

    public function handle(Issue $issue): void {
        Event::dispatch($issue);
    }
}
