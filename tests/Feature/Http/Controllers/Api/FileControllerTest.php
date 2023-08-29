<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Download;
use App\Models\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class FileControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Tests download is tracked.
     *
     * @return void
     */
    public function test_tracks_download()
    {
        $existing = Download::count();

        $name = sprintf('%s.txt', Str::uuid());

        $file = File::factory()->fromContents($name, fake()->paragraph(), true)->tracked()->create();

        $response = $this->get(sprintf('/files/%s', $file->getKey()), ['REMOTE_ADDR' => fake()->ipv4]);

        $response->assertStatus(200);

        $this->assertGreaterThan($existing, Download::count());
    }

    /**
     * Tests download isn't tracked.
     *
     * @return void
     */
    public function test_doesnt_track_download()
    {
        $existing = Download::count();

        $name = sprintf('%s.txt', Str::uuid());

        $file = File::factory()->fromContents($name, fake()->paragraph(), true)->create();

        $response = $this->get(sprintf('/files/%s', $file->getKey()), ['REMOTE_ADDR' => fake()->ipv4]);

        $response->assertStatus(200);

        $this->assertEquals($existing, Download::count());
    }
}
