<?php

namespace App\Console\Commands\User;

use App\Components\Passwords\Password;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create
                            {--p|prompt : Prompts for password}
                            {--no-confirm : Skip password confirmation prompt}
                            {--format=table : Format to display user information}
                            {--uuid= : Manually specifies the UUID for the user}
                            {name : Full name}
                            {email : E-mail address}
                            {password? : If empty, the password is generated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Execute the console command.
     */
    public function handle(Hasher $hasher)
    {
        $uuid = $this->option('uuid');
        $format = $this->option('format');
        $prompt = $this->option('prompt');
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        $possibleFormats = ['table', 'json', 'prettyJson'];

        if (! in_array($format, $possibleFormats)) {
            $this->error("The format '{$format}' is invalid.");
            $this->info('Possible values: '.implode(', ', $possibleFormats));

            return 1;
        }

        if (User::where('email', $email)->withTrashed()->count() > 0) {
            $this->error('A user with that e-mail address already exists.');

            return 1;
        }

        $generatedPassword = false;

        if ($prompt) {
            $password = $this->secret('Enter password:');

            if (! $this->option('no-confirm')) {
                $confirm = $this->secret('Enter password again:');

                if ($password !== $confirm) {
                    $this->error('Passwords do not match.');

                    return 1;
                }
            }
        } elseif (is_null($password)) {
            $password = Password::default()->generate();
            $generatedPassword = true;
        }

        $user = User::create([
            'uuid' => $uuid ?? (string) Str::uuid(),
            'name' => $name,
            'email' => $email,
            'password' => $hasher->make($password),
        ]);

        if ($format === 'table') {
            $this->info('User created successfully!');

            $rows = [
                ['ID', $user->getKey()],
                ['UUID', $user->uuid],
                ['Name', $name],
                ['Email', $email],
            ];

            // Display password if generated.
            if ($generatedPassword) {
                array_push($rows, ['Password', $password]);
            }

            $this->newLine()->table(['Name', 'Value'], $rows);
        } elseif ($format === 'json' || $format === 'prettyJson') {
            $encoded = json_encode($user->toArray(), $format === 'prettyJson' ? JSON_PRETTY_PRINT : 0);

            $this->line($encoded);
        }
    }
}
