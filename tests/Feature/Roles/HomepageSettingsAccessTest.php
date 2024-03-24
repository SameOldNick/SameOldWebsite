<?php

namespace Tests\Feature\Roles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class HomepageSettingsAccessTest extends TestCase
{
    use RefreshDatabase;
    use WithRoles;
    use WithFaker;
    use DisablesVite;

    /**
     * Tests user is authorized to get homepage metadata.
     *
     * @return void
     */
    public function testCanGetHomepageMetadata(): void
    {
        $response = $this->withRoles(['edit_profile'])->get('/api/pages/homepage');

        $response->assertSuccessful();
    }

    /**
     * Tests user is unauthorized to get homepage metadata.
     *
     * @return void
     */
    public function testCannotGetHomepageMetadata(): void
    {
        $response = $this->withRoles([])->get('/api/pages/homepage');

        $response->assertForbidden();
    }

    /**
     * Tests user is authorized to update homepage metadata.
     *
     * @return void
     */
    public function testCanPostHomepageMetadata(): void
    {
        $response = $this->withRoles(['edit_profile'])->post('/api/pages/homepage');

        $response->assertRedirect();
    }

    /**
     * Tests user is unauthorized to update homepage metadata.
     *
     * @return void
     */
    public function testCannotPostHomepageMetadata(): void
    {
        $response = $this->withRoles([])->post('/api/pages/homepage');

        $response->assertForbidden();
    }
}
