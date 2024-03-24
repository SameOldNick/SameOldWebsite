<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Technology;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class TechnologyAccessTest extends TestCase
{
    use RefreshDatabase;
    use WithRoles;
    use WithFaker;
    use DisablesVite;

    /**
     * Test accessing technologies when authorized.
     *
     * @return void
     */
    public function testCanGetTechnologies(): void
    {
        $response = $this->withRoles(['edit_profile'])->getJson('/api/technologies');

        $response->assertSuccessful();
    }

    /**
     * Test accessing technologies when unauthorized.
     *
     * @return void
     */
    public function testCannotGetTechnologies(): void
    {
        $response = $this->withRoles([])->getJson('/api/technologies');

        $response->assertForbidden();
    }

    /**
     * Test accessing a specific technology when authorized.
     *
     * @return void
     */
    public function testCanGetTechnology(): void
    {
        $technology = Technology::factory()->create();

        $response = $this->withRoles(['edit_profile'])->getJson(sprintf('/api/technologies/%d', $technology->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Test accessing a specific technology when unauthorized.
     *
     * @return void
     */
    public function testCannotGetTechnology(): void
    {
        $technology = Technology::factory()->create();

        $response = $this->withRoles([])->getJson(sprintf('/api/technologies/%d', $technology->getKey()));

        $response->assertForbidden();
    }

    /**
     * Test creating a new technology when authorized.
     *
     * @return void
     */
    public function testCanCreateTechnology(): void
    {
        $response = $this->withRoles(['edit_profile'])->postJson('/api/technologies', [
            'icon' => $this->faker->iconName(),
            'technology' => $this->faker()->unique()->technology(),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Test creating a new technology when unauthorized.
     *
     * @return void
     */
    public function testCannotCreateTechnology(): void
    {
        $response = $this->withRoles([])->postJson('/api/technologies', [
            'icon' => $this->faker->iconName(),
            'technology' => $this->faker()->unique()->technology(),
        ]);

        $response->assertForbidden();
    }

    /**
     * Test updating an existing technology when authorized.
     *
     * @return void
     */
    public function testCanUpdateTechnology(): void
    {
        $technology = Technology::factory()->create();

        $response = $this->withRoles(['edit_profile'])->putJson(sprintf('/api/technologies/%d', $technology->getKey()), [
            'icon' => $this->faker->iconName(),
            'technology' => $this->faker()->unique()->technology(),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Test updating an existing technology when unauthorized.
     *
     * @return void
     */
    public function testCannotUpdateTechnology(): void
    {
        $technology = Technology::factory()->create();

        $response = $this->withRoles([])->putJson(sprintf('/api/technologies/%d', $technology->getKey()), [
            'icon' => $this->faker->iconName(),
            'technology' => $this->faker()->unique()->technology(),
        ]);

        $response->assertForbidden();
    }

    /**
     * Test deleting a technology when authorized.
     *
     * @return void
     */
    public function testCanDeleteTechnology(): void
    {
        $technology = Technology::factory()->create();

        $response = $this->withRoles(['edit_profile'])->deleteJson(sprintf('/api/technologies/%d', $technology->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Test deleting a technology when unauthorized.
     *
     * @return void
     */
    public function testCannotDeleteTechnology(): void
    {
        $technology = Technology::factory()->create();

        $response = $this->withRoles([])->deleteJson(sprintf('/api/technologies/%d', $technology->getKey()));

        $response->assertForbidden();
    }
}
