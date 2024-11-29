<?php

namespace Tests\Feature\Roles;

use App\Models\SocialMediaLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class SocialMediaLinksAccessTest extends TestCase
{
    use DisablesVite;
    use RefreshDatabase;
    use WithFaker;
    use WithRoles;

    /**
     * Test accessing social media links when authorized.
     */
    public function test_can_get_social_mediums(): void
    {
        $response = $this->withRoles(['edit_profile'])->getJson('/api/social-media');

        $response->assertSuccessful();
    }

    /**
     * Test accessing social media links when unauthorized.
     */
    public function test_cannot_get_social_mediums(): void
    {
        $response = $this->withNoRoles()->getJson('/api/social-media');

        $response->assertForbidden();
    }

    /**
     * Test accessing a specific social media link when authorized.
     */
    public function test_can_get_social_medium(): void
    {
        $socialMedium = SocialMediaLink::factory()->create();

        $response = $this->withRoles(['edit_profile'])->getJson(sprintf('/api/social-media/%d', $socialMedium->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Test accessing a specific social media link when unauthorized.
     */
    public function test_cannot_get_social_medium(): void
    {
        $socialMedium = SocialMediaLink::factory()->create();

        $response = $this->withNoRoles()->getJson(sprintf('/api/social-media/%d', $socialMedium->getKey()));

        $response->assertForbidden();
    }

    /**
     * Test creating a new social media link when authorized.
     */
    public function test_can_create_social_medium(): void
    {
        $response = $this->withRoles(['edit_profile'])->postJson('/api/social-media', [
            'link' => $this->faker()->socialMediaLink('github'),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Test creating a new social media link when unauthorized.
     */
    public function test_cannot_create_social_medium(): void
    {
        $response = $this->withNoRoles()->postJson('/api/social-media', [
            'link' => $this->faker()->socialMediaLink('github'),
        ]);

        $response->assertForbidden();
    }

    /**
     * Test updating an existing social media link when authorized.
     */
    public function test_can_update_social_medium(): void
    {
        $socialMedium = SocialMediaLink::factory()->create();

        $response = $this->withRoles(['edit_profile'])->putJson(sprintf('/api/social-media/%d', $socialMedium->getKey()), [
            'link' => $this->faker()->socialMediaLink('github'),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Test updating an existing social media link when unauthorized.
     */
    public function test_cannot_update_social_medium(): void
    {
        $socialMedium = SocialMediaLink::factory()->create();

        $response = $this->withNoRoles()->putJson(sprintf('/api/social-media/%d', $socialMedium->getKey()), [
            'link' => $this->faker()->socialMediaLink('github'),
        ]);

        $response->assertForbidden();
    }

    /**
     * Test deleting a social media link when authorized.
     */
    public function test_can_delete_social_medium(): void
    {
        $socialMedium = SocialMediaLink::factory()->create();

        $response = $this->withRoles(['edit_profile'])->deleteJson(sprintf('/api/social-media/%d', $socialMedium->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Test deleting a social media link when unauthorized.
     */
    public function test_cannot_delete_social_medium(): void
    {
        $socialMedium = SocialMediaLink::factory()->create();

        $response = $this->withNoRoles()->deleteJson(sprintf('/api/social-media/%d', $socialMedium->getKey()));

        $response->assertForbidden();
    }
}
