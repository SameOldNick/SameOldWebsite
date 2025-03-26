<?php

namespace App\Console\Commands\Setup\Countries;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SetupCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:countries
                            {--overwrite : Clears existing countries and states before seeding}
                            {--test : Populate countries and states for testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate countries and states in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get countries data
        $countries = require __DIR__.'/countries.php';

        if ($this->option('test')) {
            $countries = Arr::where($countries, function ($value) {
                $allowed = ['CAN', 'USA', 'GBR'];

                return in_array($value['code_alpha3'], $allowed);
            });
        }

        // Clear existing data (if overwrite option is set)
        if ($this->option('overwrite')) {
            $this->info('Clearing existing data...');

            // Clear countries and states

            try {
                // Disable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=0');

                DB::table('countries')->truncate();
                DB::table('states')->truncate();
            } catch (\Exception $e) {
                throw $e;
            } finally {
                // Always re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }

            $this->info('Existing data cleared.');
        }

        $this->info($this->option('test') ? 'Populating countries and states for testing...' : 'Populating countries and states...');

        $this->withProgressBar($countries, function (array $item) {
            $countryCode = $item['code_alpha3'];

            DB::table('countries')->insert([
                'code' => $countryCode,
                'code_alpha2' => $item['code_alpha2'],
                'country' => $item['country'],
            ]);

            foreach ($item['states'] as $state) {
                DB::table('states')->insert([
                    'code' => $state['code'],
                    'state' => $state['name'],
                    'country_code' => $countryCode,
                ]);
            }
        });

        $this->newLine();
        $this->info('Countries and states seeded.');
    }
}
