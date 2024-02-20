<?php

namespace Tests\Feature\Traits;

use App\Models\User;
use App\Models\Role;

trait CreatesUser
{
    /**
     * A regular user.
     *
     * @var User
     */
    protected $user;

    /**
     * An admin user.
     *
     * @var User
     */
    protected $admin;

    /**
     * Creates the users.
     *
     * @return void
     */
    public function setUpCreatesUser()
    {
        $this->user = User::factory()->create();

        $this->admin = tap(User::factory()->create(), function ($user) {
            $user->roles()->attach(Role::firstWhere('role', 'admin'));
        });
    }

    public function tearDownCreatesUser()
    {
    }
}
