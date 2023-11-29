<?php

namespace App\Components\Security\Clerks;

use App\Components\Security\Clerks\ClerkDriver;
use App\Components\Security\Issues\Issue;
use App\Components\Security\Clerks\EloquentDriver\Issue as IssueModel;

class StackClerk implements ClerkDriver
{
    public function __construct(
        protected readonly array $stack
    )
    {

    }

    /**
     * Checks if the issue is fresh/new.
     *
     * @param Issue $issue
     * @return boolean
     */
    public function isFresh(Issue $issue): bool {
        return true;
    }

    /**
     * File the issue
     *
     * @param Issue $issue
     * @return void
     */
    public function file(Issue $issue): void {
        foreach ($this->stack as $driver) {
            if ($driver->isFresh($issue)) {
                $driver->file($issue);
            }
        }
    }
}
