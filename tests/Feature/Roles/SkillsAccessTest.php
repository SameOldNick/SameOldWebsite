<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class SkillsAccessTest extends TestCase
{
    use RefreshDatabase;
    use WithRoles;
    use WithFaker;
    use DisablesVite;

    /**
     * Tests user is authorized to get skills.
     *
     * @return void
     */
    public function testCanGetSkills(): void
    {
        $response = $this->withRoles(['edit_profile'])->getJson('/api/skills');

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get skills.
     *
     * @return void
     */
    public function testCannotGetSkills(): void
    {
        $response = $this->withRoles([])->getJson('/api/skills');

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to get skill.
     *
     * @return void
     */
    public function testCanGetSkill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->withRoles(['edit_profile'])->getJson(sprintf('/api/skills/%d', $skill->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get skill.
     *
     * @return void
     */
    public function testCannotGetSkill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->withRoles([])->getJson(sprintf('/api/skills/%d', $skill->getKey()));

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to create skills.
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
     */
    public function testCanDeleteSkill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->withRoles(['edit_profile'])->deleteJson(sprintf('/api/skills/%d', $skill->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to delete skills.
     *
     * @return void
     */
    public function testCannotDeleteSkill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->withRoles([])->deleteJson(sprintf('/api/skills/%d', $skill->getKey()));

        $response->assertForbidden();
    }
}
