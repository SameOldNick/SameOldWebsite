<?php

namespace App\Components\Security\Commands;

use App\Components\Security\Watchdogs\WatchdogDriver;
use App\Components\Security\Clerk;
use App\Components\Security\Clerks\ClerkDriver;
use App\Components\Security\Responder;
use App\Components\Security\Responders\ResponderDriver;
use Illuminate\Console\Command;
use Throwable;

class WatchdogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:check {--sniff : Find issues only} {--clerk= : Clerk driver} {--responder= : Responder driver} {watchdog}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the security check.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sniffing for issues...');

        $issues = $this->sniff($this->createWatchdog());

        $this->info('Finished sniffing.');

        if (! $this->option('sniff')) {
            $this->info('Filing issues with clerk...');

            $this->fileIssues($this->createClerk(), $issues);

            $this->info('Dispatching issues to responder...');

            $this->dispatchIssues($this->createResponder(), $issues);
        }
    }

    /**
     * Sniff for issues.
     *
     * @param WatchdogDriver $watchdog
     * @return array Found issues
     */
    protected function sniff(WatchdogDriver $watchdog) {
        $issues = [];

        try {
            $this->info('Initializing watchdog...');

            $watchdog->initialize();

            $this->info('Running watchdog...');

            $issues = $watchdog->sniff();

            $this->info(sprintf('Found %d issues.', count($issues)));
        } catch (Throwable $ex) {
            // TODO: Add issue for unknown error.
            $this->error($ex->getMessage());
        } finally {
            $this->info('Performing cleanup...');

            $watchdog->cleanup();
        }

        return $issues;
    }

    /**
     * File issues with clerk.
     *
     * @param ClerkDriver $clerk
     * @param array $issues
     * @return void
     */
    protected function fileIssues(ClerkDriver $clerk, array $issues) {
        foreach ($issues as $issue) {
            if ($clerk->isFresh($issue)) {
                $clerk->file($issue);
            }
        }
    }

    /**
     * Dispatch issues to responder.
     *
     * @param ResponderDriver $responder
     * @param array $issues
     * @return void
     */
    protected function dispatchIssues(ResponderDriver $responder, array $issues) {
        foreach ($issues as $issue) {
            if ($responder->shouldHandle($issue)) {
                $responder->handle($issue);
            }
        }
    }

    /**
     * Creates Watchdog instance.
     *
     * @return WatchdogDriver
     */
    protected function createWatchdog(): WatchdogDriver {
        return $this->getLaravel()->make('watchdog')->driver($this->argument('watchdog'));
    }

    /**
     * Creates ClerkDriver instance.
     *
     * @return ClerkDriver
     */
    protected function createClerk(): ClerkDriver {
        return $this->getLaravel()->make(Clerk::class)->driver($this->option('clerk'));
    }

    /**
     * Creates ResponderDriver instance.
     *
     * @return ResponderDriver
     */
    protected function createResponder(): ResponderDriver {
        return $this->getLaravel()->make(Responder::class)->driver($this->option('responder'));
    }
}
