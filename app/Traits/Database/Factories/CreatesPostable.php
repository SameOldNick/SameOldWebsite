<?php

namespace App\Traits\Database\Factories;

use App\Models\Post;
use App\Models\User;

trait CreatesPostable {
    /**
     * Include post with user.
     *
     * @param User|null $user
     * @param Post|null $post
     * @return boolean
     */
    public function hasPostWithUser(User $user = null, Post $post = null) {
        $post = $post ?? Post::factory(1);
        $user = $user ?? User::factory();

        return $this->has($post->for($user));
    }
}
