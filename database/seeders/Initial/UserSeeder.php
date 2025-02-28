<?php

namespace Database\Seeders\Initial;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @param  array  $additional  Any additional attributes for the user
     * @return void
     */
    public function run(array $additional = [])
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $user = User::factory([
            'uuid' => (string) Str::uuid(),
            'name' => 'Same Old Nick',
            'email' => 'admin@sameoldnick.com',
            'country_code' => 'CAN',
            'password' => '$2y$10$EdEgkPH8/.Sq0t6zrZPwnOAL8LCOgAFNw6uhb3Pgijt63fEnXDFqK', // secret
        ])->create($additional);
    }
}
