<?php

namespace App\Components\Security\Commands;

use App\Components\Security\Clerk;
use App\Components\Security\Responder;
use Illuminate\Console\Command;
use InvalidArgumentException;
use Throwable;

class WatchdogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:check {--sniff : Find issues only} {--clerk : Clerk driver} {--responder : Responder driver} {watchdog}';

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
        try {
            $watchdog = $this->getLaravel()->make('watchdog')->driver($this->argument('watchdog'));
        } catch (InvalidArgumentException $ex) {
            $this->error(sprintf('Unable to create "%s" watchdog driver: %s', $this->argument('watchdog'), $ex->getMessage()));

            return static::FAILURE;
        }

        if (! $this->option('sniff')) {
            try {
                $clerk = $this->getLaravel()->make(Clerk::class)->driver($this->option('clerk'));
            } catch (InvalidArgumentException) {
                $this->error(sprintf('Clerk driver "%s" does not exist.', $this->option('clerk') ?? '(null)'));

                return static::FAILURE;
            }

            try {
                $responder = $this->getLaravel()->make(Responder::class)->driver($this->option('responder'));
            } catch (InvalidArgumentException) {
                $this->error(sprintf('Responder driver "%s" does not exist.', $this->option('responder') ?? '(null)'));

                return static::FAILURE;
            }
        }

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

        $this->info('Finished sniffing.');

        if (! $this->option('sniff')) {
            $this->info('Filing issues with clerk...');

            foreach ($issues as $issue) {
                if ($clerk->isFresh($issue)) {
                    $clerk->file($issue);
                }
            }

            $this->info('Dispatching issues to responder...');

            foreach ($issues as $issue) {
                if ($responder->shouldHandle($issue)) {
                    $responder->handle($issue);
                }
            }
        }
    }
}
