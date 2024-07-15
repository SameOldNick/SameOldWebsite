<?php

namespace Database\Factories;

use App\Models\Commenter;
use App\Models\CommentStatus;
use App\Models\User;
use App\Traits\Database\Factories\CreatesPostable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    use CreatesPostable;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->boolean() ? $this->faker->words(3, true) : null,
            'comment' => $this->faker->realText(),
        ];
    }

    /**
     * Configure the factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this;
    }

    /**
     * Sets as registered user comment.
     *
     * @param  UserFactory|User|null  $user
     * @return static
     */
    public function registered($user = null, ?PostFactory $postFactory = null)
    {
        return $this->hasPostWithUser($user, $postFactory);
    }

    /**
     * Sets as guest user comment.
     *
     * @param  CommenterFactory|Commenter|null  $commenter
     * @return static
     */
    public function guest($commenter = null, ?PostFactory $postFactory = null)
    {
        return $this->hasPost($postFactory)->for($commenter ?? Commenter::factory());
    }

    /**
     * Sets comment status as awaiting approval
     *
     * @param  ?User  $user  Who set status
     * @return static
     */
    public function awaitingApproval($user = null)
    {
        return $this->has(CommentStatus::factory(['user_id' => $user])->awaitingApproval(), 'statuses');
    }

    /**
     * Sets comment status as awaiting verification
     *
     * @param  ?User  $user  Who set status
     * @return static
     */
    public function awaitingVerification($user = null)
    {
        return $this->has(CommentStatus::factory(['user_id' => $user])->awaitingVerification(), 'statuses');
    }

    /**
     * Sets comment status as flagged
     *
     * @param  ?User  $user  Who set status
     * @return static
     */
    public function flagged($user = null)
    {
        return $this->has(CommentStatus::factory(['user_id' => $user])->flagged(), 'statuses');
    }

    /**
     * Sets comment status as denied
     *
     * @param  ?User  $user  Who set status
     * @return static
     */
    public function denied($user = null)
    {
        return $this->has(CommentStatus::factory(['user_id' => $user])->denied(), 'statuses');
    }

    /**
     * Sets comment status as approved
     *
     * @param  ?User  $user  Who set status
     * @return static
     */
    public function approved($user = null)
    {
        return $this->has(CommentStatus::factory(['user_id' => $user])->approved(), 'statuses');
    }

    /**
     * Sets fake status
     *
     * @param  ?User  $user  Who set status
     * @param  ?\App\Enums\CommentStatus[]  $cases
     * @return static
     */
    public function fakedStatus($user = null, $cases = null)
    {
        return $this->has(CommentStatus::factory(['user_id' => $user])->fakedStatus($cases), 'statuses');
    }
}
