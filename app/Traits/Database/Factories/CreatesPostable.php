<?php

namespace App\Traits\Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

trait CreatesPostable
{
    /**
     * Include post with user.
     *
     * @param  User|null  $userFactory
     * @param  Post|null  $postFactory
     * @return static
     */
    public function hasPostWithUser($userFactory = null, ?Factory $postFactory = null)
    {
        $postFactory = $postFactory ?? Post::factory();
        $userFactory = $userFactory ?? User::factory();

        return $this->has($postFactory->for($userFactory));
    }
}
