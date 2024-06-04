<?php

namespace Tests\Feature\Roles;

use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class SkillsAccessTest extends TestCase
{
    use DisablesVite;
    use RefreshDatabase;
    use WithFaker;
    use WithRoles;

    /**
     * Tests user is authorized to get skills.
     */
    public function testCanGetSkills(): void
    {
        $response = $this->withRoles(['edit_profile'])->getJson('/api/skills');

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get skills.
     */
    public function testCannotGetSkills(): void
    {
        $response = $this->withRoles([])->getJson('/api/skills');

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to get skill.
     */
    public function testCanGetSkill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->withRoles(['edit_profile'])->getJson(sprintf('/api/skills/%d', $skill->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get skill.
     */
    public function testCannotGetSkill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->withRoles([])->getJson(sprintf('/api/skills/%d', $skill->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to create skills.
     */
    public function testCanCreateSkill(): void
    {
        $response = $this->withRoles(['edit_profile'])->postJson('/api/skills', [
            'icon' => $this->faker->iconName(),
            'skill' => $this->faker->unique()->jobTitle(),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to create skills.
     */
    public function testCannotCreateSkill(): void
    {
        $response = $this->withRoles([])->postJson('/api/skills', [
            'icon' => $this->faker->iconName(),
            'skill' => $this->faker->unique()->jobTitle(),
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to update skills.
     */
    public function testCanUpdateSkill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->withRoles(['edit_profile'])->putJson(sprintf('/api/skills/%d', $skill->getKey()), [
            'icon' => $this->faker->iconName(),
            'skill' => $this->faker->unique()->jobTitle(),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to update skills.
     */
    public function testCannotUpdateSkill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->withRoles([])->putJson(sprintf('/api/skills/%d', $skill->getKey()), [
            'skill' => $this->faker->unique()->jobTitle(),
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to delete skills.
     */
    public function testCanDeleteSkill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->withRoles(['edit_profile'])->deleteJson(sprintf('/api/skills/%d', $skill->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to delete skills.
     */
    public function testCannotDeleteSkill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->withRoles([])->deleteJson(sprintf('/api/skills/%d', $skill->getKey()));

        $response->assertForbidden();
    }
}
