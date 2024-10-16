<?php

namespace Tests\Feature\Roles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class HomepageSettingsAccessTest extends TestCase
{
    use DisablesVite;
    use RefreshDatabase;
    use WithFaker;
    use WithRoles;

    /**
     * Tests user is authorized to get homepage metadata.
     */
    public function testCanGetHomepageMetadata(): void
    {
        $response = $this->withRoles(['edit_profile'])->get('/api/pages/homepage');

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get homepage metadata.
     */
    public function testCannotGetHomepageMetadata(): void
    {
        $response = $this->withNoRoles()->get('/api/pages/homepage');

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to update homepage metadata.
     */
    public function testCanPostHomepageMetadata(): void
    {
        $response = $this->withRoles(['edit_profile'])->post('/api/pages/homepage');

        $response->assertRedirect();
    }

    /**
     * Tests user is unauthorized to update homepage metadata.
     */
    public function testCannotPostHomepageMetadata(): void
    {
        $response = $this->withNoRoles()->post('/api/pages/homepage');

        $response->assertForbidden();
    }
}
