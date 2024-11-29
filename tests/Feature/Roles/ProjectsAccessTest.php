<?php

namespace Tests\Feature\Roles;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class ProjectsAccessTest extends TestCase
{
    use DisablesVite;
    use RefreshDatabase;
    use WithFaker;
    use WithRoles;

    /**
     * Test accessing projects when authorized.
     */
    public function test_can_get_projects(): void
    {
        $response = $this->withRoles(['edit_profile'])->getJson('/api/projects');

        $response->assertSuccessful();
    }

    /**
     * Test accessing projects when unauthorized.
     */
    public function test_cannot_get_projects(): void
    {
        $response = $this->withNoRoles()->getJson('/api/projects');

        $response->assertForbidden();
    }

    /**
     * Test accessing a specific project when authorized.
     */
    public function test_can_get_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->withRoles(['edit_profile'])->getJson(sprintf('/api/projects/%d', $project->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Test accessing a specific project when unauthorized.
     */
    public function test_cannot_get_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->withNoRoles()->getJson(sprintf('/api/projects/%d', $project->getKey()));

        $response->assertForbidden();
    }

    /**
     * Test creating a new project when authorized.
     */
    public function test_can_create_project(): void
    {
        $response = $this->withRoles(['edit_profile'])->postJson('/api/projects', [
            'title' => Str::headline($this->faker->realText(25)),
            'description' => $this->faker->realText(),
            'url' => $this->faker->url(),
            'tags' => $this->faker->words(5),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Test creating a new project when unauthorized.
     */
    public function test_cannot_create_project(): void
    {
        $response = $this->withNoRoles()->postJson('/api/projects', [
            'title' => Str::headline($this->faker->realText(25)),
            'description' => $this->faker->realText(),
            'url' => $this->faker->url(),
        ]);

        $response->assertForbidden();
    }

    /**
     * Test updating an existing project when authorized.
     */
    public function test_can_update_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->withRoles(['edit_profile'])->putJson(sprintf('/api/projects/%d', $project->getKey()), [
            'title' => Str::headline($this->faker->realText(25)),
            'description' => $this->faker->realText(),
            'url' => $this->faker->url(),
            'tags' => $this->faker->words(5),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Test updating an existing project when unauthorized.
     */
    public function test_cannot_update_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->withNoRoles()->putJson(sprintf('/api/projects/%d', $project->getKey()), [
            'title' => Str::headline($this->faker->realText(25)),
            'description' => $this->faker->realText(),
            'url' => $this->faker->url(),
            'tags' => $this->faker->words(5),
        ]);

        $response->assertForbidden();
    }

    /**
     * Test deleting a project when authorized.
     */
    public function test_can_delete_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->withRoles(['edit_profile'])->deleteJson(sprintf('/api/projects/%d', $project->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Test deleting a project when unauthorized.
     */
    public function test_cannot_delete_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->withNoRoles()->deleteJson(sprintf('/api/projects/%d', $project->getKey()));

        $response->assertForbidden();
    }
}
