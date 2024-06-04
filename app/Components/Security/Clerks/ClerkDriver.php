<?php

namespace App\Components\Security\Clerks;

use App\Components\Security\Issues\Issue;

interface ClerkDriver
{
    /**
     * Checks if the issue is fresh/new.
     */
    public function isFresh(Issue $issue): bool;

    /**
     * File the issue
     */
    public function file(Issue $issue): void;
}
