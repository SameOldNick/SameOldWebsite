<?php

namespace App\Components\Security\Responders;

use App\Components\Security\Issues\Issue;

interface ResponderDriver
{
    /**
     * Determines if issue should be handled.
     */
    public function shouldHandle(Issue $issue): bool;

    /**
     * Handles issue.
     */
    public function handle(Issue $issue): void;
}
