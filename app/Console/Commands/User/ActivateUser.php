<?php

namespace App\Console\Commands\User;

use App\Models\User;
use Illuminate\Console\Command;

class ActivateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:activate
                            {email : The email of the user to activate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activates user';

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

        if (! $user) {
            $this->error('User not found.');

            return;
        }

        if (! $user->trashed()) {
            $this->error('User is already activated.');

            return 1;
        }

        if ($user->restore()) {
            $this->info('User activated successfully!');
        } else {
            $this->error('An error occurred activating user.');

            return 1;
        }
    }
}
