<?php

namespace Database\Factories;

use App\Models\SocialMediaLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SocialMediaLink>
 */
class SocialMediaLinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'link' => $this->faker->unique()->socialMediaLink(),
        ];
    }
}
