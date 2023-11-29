<?php

namespace App\Components\Security\Clerks;

use App\Components\Security\Issues\Issue;

interface ClerkDriver
{
    /**
     * Checks if the issue is fresh/new.
     *
     * @param Issue $issue
     * @return bool
     */
    public function isFresh(Issue $issue): bool;

    /**
     * File the issue
     *
     * @param Issue $issue
     * @return void
     */
    public function file(Issue $issue): void;
}
