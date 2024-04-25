<?php

namespace App\Console\Commands\User;

use App\Models\User;

use Illuminate\Console\Command;

class DeactivateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:deactivate
                            {email : The email of the user to deactivate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivates user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        /**
         * @var User|null
         */
        $user = User::firstWhere('email', $email);

        if (!$user) {
            $this->error('User not found.');
            return;
        }

        if ($user->trashed()) {
            $this->error('User is already deactivated.');
            return 1;
        }

        if ($user->delete()) {
            $this->info('User deactivated successfully!');
        } else {
            $this->error('An error occurred deactivating user.');
            return 1;
        }

    }
}
