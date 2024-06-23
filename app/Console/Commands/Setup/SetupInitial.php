<?php

namespace App\Console\Commands\Setup;

use App\Components\Passwords\Password;
use App\Models\User;
use App\Traits\Console\CreatesSeeders;
use Database\Seeders\InitialSeeder;
use Illuminate\Console\Command;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;

class SetupInitial extends Command
{
    use CreatesSeeders;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:initial
                            {--user-uuid= : Specifies the users UUID}
                            {--user-name=Same Old User : The name of the user}
                            {--user-email=admin@sameoldnick.com : The users e-mail address}
                            {--user-password=generate : Where to get password from (generate|prompt|hash|plaintext)}
                            {--user-password-passed= : Specifies the users password (hashed or plaintext)}
                            {--skip-user : Skip creating the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeds the database with the initial data';

    /**
     * Execute the console command.
     */
    public function handle(Hasher $hasher)
    {
        // Extract options
        $skipUser = $this->option('skip-user');
        $userUuid = $this->option('user-uuid');
        $userName = $this->option('user-name');
        $userEmail = $this->option('user-email');
        $userPasswordSource = $this->option('user-password');

        if (! $skipUser) {
            if (empty($userName)) {
                $this->error('The --user-name option is empty.');

                return 1;
            }

            if (empty($userEmail)) {
                $this->error('The --user-email option is empty.');

                return 1;
            }

            $password = $this->gatherPassword($userPasswordSource);

            // Validate password
            if ($userPasswordSource !== 'hash') {
                $this->validatePassword($password);
            }

            // Create initial user
            $this->info('Creating initial user...');

            $user = User::create([
                'uuid' => $userUuid ?? (string) Str::uuid(),
                'name' => $userName,
                'email' => $userEmail,
                'password' => $userPasswordSource !== 'hash' ? $hasher->make($password) : $password,
            ]);

            // Display user details
            $this->line('User details:');
            $this->table(['Name', 'Value'], [
                ['UUID', $user->uuid],
                ['Name', $user->name],
                ['Email', $user->email],
                ['Password', $userPasswordSource === 'generate' ? $password : str_repeat('*', 10)],
            ]);
        } else {
            $this->info('Skipped creating initial user.');
        }

        // Run initial seeder
        $this->info('Seeding initial data into database...');

        $initialSeeder = $this->createSeeder(InitialSeeder::class);

        $initialSeeder(['user' => $user ?? null]);

        $this->info('Initial seeding complete.');
    }

    /**
     * Gathers password
     *
     * @param  string  $source  Where to pull password
     * @return string
     */
    protected function gatherPassword(string $source)
    {
        $passed = $this->option('user-password-passed');

        if (! in_array($source, ['generate', 'prompt', 'hash', 'plain-text'])) {
            $this->error('The --user-password-source option is invalid.');

            exit(1);
        }

        if (in_array($source, ['hash', 'plain-text']) && empty($passed)) {
            $this->error('The --user-password-passed option must be set when password source is "hash" or "plain-text".');

            exit(1);
        }

        switch ($source) {
            case 'prompt':
                $password = $this->secret('Enter password:');
                $passwordConfirm = $this->secret('Enter password again:');

                if ($password !== $passwordConfirm) {
                    $this->error('Passwords do not match. Please try again...');

                    exit(1);
                }

                break;

            case 'generate':
                $password = Password::default()->generate();

                break;

            default:
                $password = $passed;

                break;
        }

        return $password;
    }

    /**
     * Validates password
     *
     * @param  string  $password
     * @return void
     */
    protected function validatePassword(#[\SensitiveParameter] string $password)
    {
        $validator = Validator::make(['password' => $password], ['password' => RulesPassword::required()]);

        if ($validator->fails()) {
            $this->error('Password is invalid: ');
            $this->error($validator->messages()->first());

            exit(1);
        }
    }
}
