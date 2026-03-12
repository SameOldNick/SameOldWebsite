<?php

namespace App\Components\Security\Watchdogs;

use App\Components\Security\Issues\Issue;

interface WatchdogDriver
{
    /**
     * Initializes the watchdog.
     */
    public function initialize(): void;

    /**
     * Sniff for issues.
     *
     * @return array<Issue>
     */
    public function sniff(): array;

    /**
     * Cleans up with watchdog.
     *
     * @return void
     */
    public function cleanup();
}
