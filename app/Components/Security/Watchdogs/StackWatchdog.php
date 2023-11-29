<?php

namespace App\Components\Security\Watchdogs;

final class StackWatchdog implements WatchdogDriver
{
    public function __construct(
        protected readonly array $drivers
    ) {
    }

    /**
     * Initializes the watchdog.
     *
     * @return void
     */
    public function initialize(): void
    {
        foreach ($this->drivers as $driver) {
            $driver->initialize();
        }
    }

    /**
     * Sniff for issues.
     *
     * @return array<\App\Components\Security\Issues\Issue>
     */
    public function sniff(): array
    {
        $issues = collect();

        foreach ($this->drivers as $driver) {
            $issues->push($driver->sniff());
        }

        return $issues->flatten()->all();
    }

    /**
     * Cleans up with watchdog.
     *
     * @return void
     */
    public function cleanup()
    {
        foreach ($this->drivers as $driver) {
            $driver->cleanup();
        }
    }
}
