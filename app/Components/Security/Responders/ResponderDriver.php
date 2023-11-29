<?php

namespace App\Components\Security\Responders;

use App\Components\Security\Issues\Issue;

interface ResponderDriver
{
    /**
     * Determines if issue should be handled.
     *
     * @param Issue $issue
     * @return bool
     */
    public function shouldHandle(Issue $issue): bool;

    /**
     * Handles issue.
     *
     * @param Issue $issue
     * @return void
     */
    public function handle(Issue $issue): void;
}
