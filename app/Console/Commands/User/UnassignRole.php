<?php

namespace App\Console\Commands\User;

use App\Models\Role;
use App\Models\User;

use Illuminate\Console\Command;

class UnassignRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:unassign-role {email} {roles*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unassign role(s) from a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $roleNames = $this->argument('roles');

        /**
         * @var User|null
         */
        $user = User::firstWhere('email', $email);

        if (!$user) {
            $this->error('User not found.');
            return 1;
        }

        $roleIds = [];

        foreach ($roleNames as $roleName) {
            $role = Role::firstWhere('role', $roleName);

            if (!$role) {
                $this->error("Role '$roleName' not found.");
                return;
            }

            array_push($roleIds, $role->getKey());
        }

        $user->roles()->detach($roleIds);

        $this->info('Role(s) unassigned successfully!');
    }
}
