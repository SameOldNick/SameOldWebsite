<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(User $user = null): void
    {
        // Laravel might try to dig up a non-existent model, so also check if it exists.
        if (is_null($user) || ! $user->exists) {
            $user = $this->createUser();
        }

        $this->call([
            Initial\ProjectSeeder::class,
            Initial\SkillSeeder::class,
            Initial\TechnologySeeder::class,
        ]);

        $this->callWith(Initial\RoleUserSeeder::class, ['user' => $user]);
        $this->callWith(Initial\ArticleSeeder::class, ['user' => $user]);
    }

    /**
     * Create user
     *
     * @return User
     */
    protected function createUser()
    {
        $uuid = (string) Str::uuid();

        $this->callWith(Initial\UserSeeder::class, ['additional' => ['uuid' => $uuid]]);

        return User::firstWhere('uuid', $uuid);
    }
}
