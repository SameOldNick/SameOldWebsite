<?php

namespace Tests\Feature\Traits;

use App\Models\User;

trait CreatesUser
{
    protected $user;

    public function setUpCreatesUser()
    {
        $this->user = User::factory()->create();
    }

    public function tearDownCreatesUser()
    {
    }
}
