<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use App\Notifications\Alert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SendAlertCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alert:send
                            {--all-users : Sends alert to all users}
                            {--user= : Sends alert to user with UUID}
                            {--role= : Sends alert to users with role}
                            {--color=info : Colour of alert}
                            {--link= : Link to include with alert}
                            {message : Message to send}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an alert to user(s)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = $this->getUsers();

        $color = $this->option('color');
        $link = $this->option('link');
        $message = $this->argument('message');

        $possibleColors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

        if (! in_array($color, $possibleColors)) {
            $this->error(sprintf('Color must be one of the following: %s', implode(', ', $possibleColors)));

            return 1;
        }

        if ($link && ! Str::isUrl($link)) {
            $this->error(sprintf("The url '%s' is invalid.", $link));

            return 1;
        }

        foreach ($users as $user) {
            $notification = Alert::create(
                $color,
                $message,
                $link
            );

            Notification::send($user, $notification);

            $this->info(sprintf("Sent alert to user with UUID '%s'.", $user->uuid));
        }

        $this->info('Finished sending alerts.');
    }

    /**
     * Gets the users
     *
     * @return \Illuminate\Support\Collection<int, User>
     */
    protected function getUsers()
    {
        if ($this->option('all-users')) {
            return User::all();
        } elseif ($this->option('user')) {
            $user = User::firstWhere('uuid', $this->option('user'));

            if (! $user) {
                $this->error(sprintf('User with UUID "%s" could not be found.', $this->option('user')));

                exit(1);
            }

            return collect([$user]);
        } elseif ($this->option('role')) {
            $role = Role::firstWhere('role', $this->option('role'));

            if (! $role) {
                $this->error(sprintf('The role "%s" could not be found.', $this->option('role')));

                exit(1);
            }

            return $role->users;
        }

        $this->error('The --all-users, --user, or --role option must be specified.');

        exit(1);
    }
}
