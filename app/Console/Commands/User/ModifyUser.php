<?php

namespace App\Console\Commands\User;

use App\Components\Passwords\Password;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ModifyUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:modify
                            {email : The email of the user to modify}
                            {--name= : The new name}
                            {--new-email= : The new email}
                            {--password= : The new password}
                            {--random-password : Sets password to randomly generated password}
                            {--prompt : Prompt for new password}
                            {--no-confirm-password : Skip password confirmation prompt}
                            {--state= : The new state}
                            {--country= : The new country}
                            {--no-confirm-save : Skip save confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Modify user details';

    /**
     * Execute the console command.
     */
    public function handle(Hasher $hasher)
    {
        $email = $this->argument('email');

        /**
         * @var User|null
         */
        $user = User::firstWhere('email', $email);

        if (! $user) {
            $this->error('User not found.');

            return;
        }

        $name = $this->option('name');
        $newEmail = $this->option('new-email');
        $stateCode = $this->option('state');
        $countryCode = $this->option('country');

        if (! empty($name)) {
            $user->name = $name;
        }

        if (! empty($newEmail)) {
            $user->email = $newEmail;
        }

        if ($this->option('prompt')) {
            $password = $this->secret('Enter password:');

            if (! $this->option('no-confirm-password')) {
                $confirm = $this->secret('Enter password again:');

                if ($password !== $confirm) {
                    $this->error('Passwords do not match.');

                    return 1;
                }
            }

            $user->password = $hasher->make($password);
        } elseif ($this->option('password')) {
            $user->password = $hasher->make($this->option('password'));
        } else if ($this->option('random-password')) {
            $generatedPassword = Password::default()->generate();

            $user->password = $hasher->make($generatedPassword);
        }

        if (! empty($stateCode) || ! empty($countryCode)) {
            $country = ! empty($countryCode) ? Country::firstWhere('code', $countryCode) : $user->country;

            if (! empty($stateCode)) {
                $state = $country->states()->firstWhere('code', $stateCode);

                if (is_null($state)) {
                    $this->error("Could not find state code '{$stateCode}' for country '{$country->country}'.");

                    return 1;
                }

                $user->state()->associate($state);
            }

            $user->country()->associate($country);
        }

        if (!$user->isDirty() && !isset($generatedPassword)) {
            $this->info('No changes were made to the user.');
            return 0;
        }
        
        $this->info('The following changes will be made to the user: ');

        $dirty = $user->getDirty();

        if (isset($generatedPassword)) {
            unset($dirty['password']);
        }

        $rows = Arr::map($dirty, fn ($value, $column) => [$this->getColumnName($column), $this->getColumnValue($column, $value)]);

        if (isset($generatedPassword)) {
            array_push($rows, ['Password', $generatedPassword]);
        }

        $this->table(['Column', 'New Value'], $rows);

        if (! $this->option('no-confirm-save') && ! $this->confirm('Would you like to save these changes?')) {
            return 0;
        }

        $user->save();

        $this->info('User details updated successfully!');
    }

    /**
     * Gets displayable column name
     *
     * @param string $column
     * @return string
     */
    protected function getColumnName(string $column): string
    {
        $mutators = [
            'state_id' => 'State',
            'country_code' => 'Country',
        ];

        return isset($mutators[$column]) ? $mutators[$column] : Str::title($column);
    }

    /**
     * Gets displayable column value
     *
     * @param string $column
     * @param string $original
     * @return string
     */
    protected function getColumnValue(string $column, $original): string
    {
        $mutators = [
            'state_id' => fn ($id) => State::find($id)->state,
            'country_code' => fn ($code) => Country::firstWhere('code', $code)->country,
            'password' => fn ($password) => '************',
        ];

        return isset($mutators[$column]) ? value($mutators[$column], $original, $column) : $original;
    }
}
