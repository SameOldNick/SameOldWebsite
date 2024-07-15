<?php

namespace Database\Factories;

use App\Enums\CommentStatus as CommentStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommentStatus>
 */
class CommentStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    /**
     * Sets status as awaiting approval
     *
     * @return static
     */
    public function awaitingApproval() {
        return $this->status(CommentStatusEnum::AwaitingApproval);
    }

    /**
     * Sets status as awaiting verification
     *
     * @return static
     */
    public function awaitingVerification() {
        return $this->status(CommentStatusEnum::AwaitingVerification);
    }

    /**
     * Sets status as flagged
     *
     * @return static
     */
    public function flagged() {
        return $this->status(CommentStatusEnum::Flagged);
    }

    /**
     * Sets status as denied
     *
     * @return static
     */
    public function denied() {
        return $this->status(CommentStatusEnum::Denied);
    }

    /**
     * Sets status as approved
     *
     * @return static
     */
    public function approved() {
        return $this->status(CommentStatusEnum::Approved);
    }

    /**
     * Sets status
     *
     * @param CommentStatusEnum $status
     * @return static
     */
    public function status(CommentStatusEnum $status) {
        return $this->state([
            'status' => $status
        ]);
    }

    /**
     * Sets fake status
     *
     * @param ?CommentStatusEnum[] $cases
     * @return static
     */
    public function fakedStatus($cases = null) {
        $cases = $cases ?? CommentStatusEnum::cases();

        return $this->state(fn () => ['status' => Arr::random($cases)]);
    }
}
