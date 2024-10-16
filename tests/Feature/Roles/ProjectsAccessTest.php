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
    public function testCanGetProjects(): void
    {
        $response = $this->withRoles(['edit_profile'])->getJson('/api/projects');

        $response->assertSuccessful();
    }

    /**
     * Test accessing projects when unauthorized.
     */
    public function testCannotGetProjects(): void
    {
        $response = $this->withNoRoles()->getJson('/api/projects');

        $response->assertForbidden();
    }

    /**
     * Test accessing a specific project when authorized.
     */
    public function testCanGetProject(): void
    {
        $project = Project::factory()->create();

        $response = $this->withRoles(['edit_profile'])->getJson(sprintf('/api/projects/%d', $project->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Test accessing a specific project when unauthorized.
     */
    public function testCannotGetProject(): void
    {
        $project = Project::factory()->create();

        $response = $this->withNoRoles()->getJson(sprintf('/api/projects/%d', $project->getKey()));

        $response->assertForbidden();
    }

    /**
     * Test creating a new project when authorized.
     */
    public function testCanCreateProject(): void
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
    public function testCannotCreateProject(): void
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
    public function testCanUpdateProject(): void
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
    public function testCannotUpdateProject(): void
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
    public function testCanDeleteProject(): void
    {
        $project = Project::factory()->create();

        $response = $this->withRoles(['edit_profile'])->deleteJson(sprintf('/api/projects/%d', $project->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Test deleting a project when unauthorized.
     */
    public function testCannotDeleteProject(): void
    {
        $project = Project::factory()->create();

        $response = $this->withNoRoles()->deleteJson(sprintf('/api/projects/%d', $project->getKey()));

        $response->assertForbidden();
    }
}
