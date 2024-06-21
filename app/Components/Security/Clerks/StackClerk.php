<?php

namespace App\Components\Security\Clerks;

use App\Components\Security\Issues\Issue;

final class StackClerk implements ClerkDriver
{
    public function __construct(
        protected readonly array $stack
    ) {}

    /**
     * Checks if the issue is fresh/new.
     */
    public function isFresh(Issue $issue): bool
    {
        return true;
    }

    /**
     * File the issue
     */
    public function file(Issue $issue): void
    {
        foreach ($this->stack as $driver) {
            if ($driver->isFresh($issue)) {
                $driver->file($issue);
            }
        }
    }
}
