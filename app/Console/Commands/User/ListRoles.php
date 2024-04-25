<?php

namespace App\Console\Commands\User;

use App\Models\Role;
use App\Models\User;

use Illuminate\Console\Command;

class ListRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:roles {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Displays roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if ($email) {
            /**
             * @var User|null
             */
            $user = User::firstWhere('email', $email);

            if (!$user) {
                $this->error('User not found.');
                return 1;
            }

            $roles = $user->roles;

            $this->info("Roles for '{$email}':");
        } else {
            $roles = Role::all();
        }

        $this->table(['ID', 'Name'], $roles->map(fn ($role) => [$role->getKey(), $role->role]));

    }
}
