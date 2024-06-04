<?php

namespace App\Components\Security\Watchdogs;

interface WatchdogDriver
{
    /**
     * Initializes the watchdog.
     */
    public function initialize(): void;

    /**
     * Sniff for issues.
     *
     * @return array<\App\Components\Security\Issues\Issue>
     */
    public function sniff(): array;

    /**
     * Cleans up with watchdog.
     *
     * @return void
     */
    public function cleanup();
}
