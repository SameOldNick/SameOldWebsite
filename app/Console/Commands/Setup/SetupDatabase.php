<?php

namespace App\Console\Commands\Setup;

use Illuminate\Console\Command;

class SetupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:database
                            {--y|yes : Proceed without asking for confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the database including migrations and seeding';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Extract options
        $skipPrompt = $this->option('yes');

        // Function to confirm database setup
        $this->confirmDatabaseSetup($skipPrompt);

        // Run migrations
        $this->info('Running database migrations...');
        $this->call('migrate:fresh', []);

        // Populate countries and states
        $this->info('Populating countries and states...');
        $this->call('setup:countries');

        // Seed the database
        $this->info('Seeding the database...');
        $this->call('db:seed', ['--class' => 'SetupSeeder']);

        $this->info('Database setup complete.');
    }

    /**
     * Confirms database setup
     *
     * @return void
     */
    private function confirmDatabaseSetup(bool $skipPrompt)
    {
        if (! $skipPrompt) {
            if (! $this->confirm('This will erase everything in the database. Are you sure you want to continue?')) {
                $this->error('Cancelled database setup.');
                exit(1);
            }
        }
    }
}
