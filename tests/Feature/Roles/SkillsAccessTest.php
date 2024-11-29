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
    public function test_can_get_skills(): void
    {
        $response = $this->withRoles(['edit_profile'])->getJson('/api/skills');

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get skills.
     */
    public function test_cannot_get_skills(): void
    {
        $response = $this->withNoRoles()->getJson('/api/skills');

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to get skill.
     */
    public function test_can_get_skill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->withRoles(['edit_profile'])->getJson(sprintf('/api/skills/%d', $skill->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get skill.
     */
    public function test_cannot_get_skill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->withNoRoles()->getJson(sprintf('/api/skills/%d', $skill->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to create skills.
     */
    public function test_can_create_skill(): void
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
    public function test_cannot_create_skill(): void
    {
        $response = $this->withNoRoles()->postJson('/api/skills', [
            'icon' => $this->faker->iconName(),
            'skill' => $this->faker->unique()->jobTitle(),
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to update skills.
     */
    public function test_can_update_skill(): void
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
    public function test_cannot_update_skill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->withNoRoles()->putJson(sprintf('/api/skills/%d', $skill->getKey()), [
            'skill' => $this->faker->unique()->jobTitle(),
        ]);

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to delete skills.
     */
    public function test_can_delete_skill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->withRoles(['edit_profile'])->deleteJson(sprintf('/api/skills/%d', $skill->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to delete skills.
     */
    public function test_cannot_delete_skill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->withNoRoles()->deleteJson(sprintf('/api/skills/%d', $skill->getKey()));

        $response->assertForbidden();
    }
}
