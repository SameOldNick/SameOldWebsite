<?php

namespace App\Console\Commands\Setup;

use Illuminate\Console\Command;

class SetupProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:project
                            {--y|yes : Skip confirmation prompts}
                            {--no-initial : Skip running the initial seeder}
                            {--package-manager=yarn : The package manager to use (npm|yarn|pnpm)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the project including database setup and frontend build';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Extract options
        $skipPrompts = $this->option('yes');
        $runInitialSeeder = ! $this->option('no-initial');
        $packageManager = $this->option('package-manager');

        if (! in_array($packageManager, ['npm', 'yarn', 'pnpm'])) {
            $this->error('Invalid package manager.');

            return 1;
        }

        // This command won't work without the composer packages installed.

        // Confirm the .env file is configured
        $this->confirmEnvSetup($skipPrompts);

        // Setup database
        $this->info('Setting up database...');
        $this->call('setup:database', [
            '--yes' => $skipPrompts,
        ]);

        // Seed database with initial data
        if ($runInitialSeeder) {
            $this->call('setup:initial');
        }

        // Build front-end
        $this->info('Building front-end...');
        $this->call('setup:frontend', [
            // No need to specify --cmd-prefix as the commands will be run in the same context as this command.
            'package-manager' => $packageManager,
        ]);

        // Create symbolic link to storage directory
        $this->info('Creating symbolic link to storage directory...');
        $this->call('storage:link', ['--force' => true]);

        $this->info('Setup complete.');
    }

    /**
     * Prompts user has updated .env file.
     *
     * @param bool $skipPrompt
     * @return void
     */
    private function confirmEnvSetup(bool $skipPrompt)
    {
        if (! $skipPrompt) {
            if (! $this->confirm('Have you updated the .env configuration variables?')) {
                $this->error('Please update the .env configuration variables and run the command again.');
                exit(1);
            }
        }
    }
}
