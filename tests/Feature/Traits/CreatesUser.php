<?php

namespace Tests\Feature\Traits;

use App\Models\Role;
use App\Models\User;

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
        $this->user = $this->createUser();

        $this->admin = $this->createUser(Role::all());
    }

    public function createUser($roles = [])
    {
        $factory = User::factory();

        if (! empty($roles)) {
            $factory = $factory->hasRoles($roles);
        }

        return $factory->create();
    }

    public function tearDownCreatesUser()
    {
    }
}
