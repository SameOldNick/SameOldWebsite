<?php

namespace App\Traits\Database\Factories;

use App\Models\Person;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

trait CreatesPostable
{
    /**
     * Include post without user.
     *
     * @return static
     */
    public function hasPost(?Factory $postFactory = null)
    {
        $postFactory = $postFactory ?? Post::factory();

        return $this->has($postFactory);
    }

    /**
     * Create post with person.
     *
     * @param  Person|null  $personFactory
     * @param  Post|null  $postFactory
     * @return $this
     */
    public function createPostWithPerson($personFactory = null, ?Factory $postFactory = null)
    {
        $postFactory = $postFactory ?? Post::factory();
        $personFactory = $personFactory ?? Person::factory();

        return $this->has($postFactory->for($personFactory));
    }

    /**
     * Creates post with a registered user
     *
     * @param  User|null  $user  User to associate or if null, a new user.
     * @return $this
     */
    public function createPostWithRegisteredPerson($user = null, ?Factory $postFactory = null)
    {
        return $this->createPostWithPerson(
            Person::factory()->user($user),
            $postFactory
        );
    }

    /**
     * Creates post with a guest person
     *
     * @param  bool  $verified  If true, sets email as verified.
     * @return $this
     */
    public function createPostWithGuestPerson(bool $verified = false, ?Factory $postFactory = null)
    {
        return $this->createPostWithPerson(
            $verified ? Person::factory()->guest()->verified() : Person::factory()->guest(),
            $postFactory
        );
    }
}
