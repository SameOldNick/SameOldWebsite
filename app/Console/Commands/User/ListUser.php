<?php

namespace App\Console\Commands\User;

use App\Models\User;
use Illuminate\Console\Command;

class ListUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:list {--display=* : Display column}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Displays users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $display = $this->option('display');

        $possibleColumns = [
            'id' => 'ID',
            'uuid' => 'UUID',
            'name' => 'Name',
            'avatar' => 'Avatar',
            'state' => 'State',
            'country_code' => 'Country',
            'email' => 'Email',
            'email_verified_at' => 'Email Verified At',
            'password' => 'Password',
            'remember_token' => 'Remember Token',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];

        $headers = [];
        $columns = [];

        if (! empty($display)) {
            foreach ($display as $key) {
                if (! isset($possibleColumns[$key])) {
                    $this->error("Column key '$key' does not exist.");

                    return 1;
                }

                array_push($headers, $possibleColumns[$key]);
                array_push($columns, $key);
            }
        } else {
            $headers = array_values($possibleColumns);
            $columns = array_keys($possibleColumns);
        }

        $this->table($headers, User::all()->select(...$columns));
    }
}
