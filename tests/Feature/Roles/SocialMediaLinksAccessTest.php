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
    public function testCanGetSocialMediums(): void
    {
        $response = $this->withRoles(['edit_profile'])->getJson('/api/social-media');

        $response->assertSuccessful();
    }

    /**
     * Test accessing social media links when unauthorized.
     */
    public function testCannotGetSocialMediums(): void
    {
        $response = $this->withRoles([])->getJson('/api/social-media');

        $response->assertForbidden();
    }

    /**
     * Test accessing a specific social media link when authorized.
     */
    public function testCanGetSocialMedium(): void
    {
        $socialMedium = SocialMediaLink::factory()->create();

        $response = $this->withRoles(['edit_profile'])->getJson(sprintf('/api/social-media/%d', $socialMedium->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Test accessing a specific social media link when unauthorized.
     */
    public function testCannotGetSocialMedium(): void
    {
        $socialMedium = SocialMediaLink::factory()->create();

        $response = $this->withRoles([])->getJson(sprintf('/api/social-media/%d', $socialMedium->getKey()));

        $response->assertForbidden();
    }

    /**
     * Test creating a new social media link when authorized.
     */
    public function testCanCreateSocialMedium(): void
    {
        $response = $this->withRoles(['edit_profile'])->postJson('/api/social-media', [
            'link' => $this->faker()->socialMediaLink('github'),
        ]);

        $response->assertSuccessful();
    }

    /**
     * Test creating a new social media link when unauthorized.
     */
    public function testCannotCreateSocialMedium(): void
    {
        $response = $this->withRoles([])->postJson('/api/social-media', [
            'link' => $this->faker()->socialMediaLink('github'),
        ]);

        $response->assertForbidden();
    }

    /**
     * Test updating an existing social media link when authorized.
     */
    public function testCanUpdateSocialMedium(): void
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
    public function testCannotUpdateSocialMedium(): void
    {
        $socialMedium = SocialMediaLink::factory()->create();

        $response = $this->withRoles([])->putJson(sprintf('/api/social-media/%d', $socialMedium->getKey()), [
            'link' => $this->faker()->socialMediaLink('github'),
        ]);

        $response->assertForbidden();
    }

    /**
     * Test deleting a social media link when authorized.
     */
    public function testCanDeleteSocialMedium(): void
    {
        $socialMedium = SocialMediaLink::factory()->create();

        $response = $this->withRoles(['edit_profile'])->deleteJson(sprintf('/api/social-media/%d', $socialMedium->getKey()));

        $response->assertSuccessful();
    }

    /**
     * Test deleting a social media link when unauthorized.
     */
    public function testCannotDeleteSocialMedium(): void
    {
        $socialMedium = SocialMediaLink::factory()->create();

        $response = $this->withRoles([])->deleteJson(sprintf('/api/social-media/%d', $socialMedium->getKey()));

        $response->assertForbidden();
    }
}
