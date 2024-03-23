<?php

namespace Tests\Feature\Http\Controllers\Api;

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

    public function testCanGetHomepageMetadata(): void
    {
        $response = $this->withRoles(['edit_profile'])->get('/api/pages/homepage');

        $response->assertSuccessful();
    }

    public function testCannotGetHomepageMetadata(): void
    {
        $response = $this->withRoles([])->get('/api/pages/homepage');

        $response->assertForbidden();
    }

    public function testCanPostHomepageMetadata(): void
    {
        $response = $this->withRoles(['edit_profile'])->post('/api/pages/homepage');

        $response->assertRedirect();
    }

    public function testCannotPostHomepageMetadata(): void
    {
        $response = $this->withRoles([])->post('/api/pages/homepage');

        $response->assertForbidden();
    }
}
