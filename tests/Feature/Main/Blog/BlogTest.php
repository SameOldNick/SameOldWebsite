<?php

namespace Tests\Feature\Main\Blog;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Tests viewing a draft article.
     *
     * @return void
     */
    public function test_view_draft_article()
    {
        $this->withoutVite();

        $article = Article::factory()->createPostWithRegisteredPerson()->withRevision()->create();

        $response = $this->get(route('blog.single', ['article' => $article]));

        $response->assertNotFound();
    }

    /**
     * Tests viewing a published article.
     *
     * @return void
     */
    public function test_view_published_article()
    {
        $this->withoutVite();

        $article = Article::factory()->createPostWithRegisteredPerson()->withRevision()->published()->create();

        $response = $this->get(route('blog.single', ['article' => $article]));

        $response->assertSuccessful();
    }

    /**
     * Tests viewing a scheduled article.
     *
     * @return void
     */
    public function test_view_scheduled_article()
    {
        $this->withoutVite();

        $article = Article::factory()->createPostWithRegisteredPerson()->withRevision()->published($this->faker->dateTimeBetween('+1 week', '+3 weeks'))->create();

        $response = $this->get(route('blog.single', ['article' => $article]));

        $response->assertNotFound();
    }

    /**
     * Tests a scheduled article is viewable after date.
     *
     * @return void
     */
    public function test_view_scheduled_article_in_future()
    {
        $this->withoutVite();

        $published = Carbon::instance($this->faker->dateTimeBetween('+1 week', '+3 weeks'));

        $article = Article::factory()->createPostWithRegisteredPerson()->withRevision()->published($published)->create();

        $this->travelTo($published->addMinute(1));

        $response = $this->get(route('blog.single', ['article' => $article]));

        $response->assertSuccessful();
    }

    /**
     * Tests a deleted article is not publically viewable.
     *
     * @return void
     */
    public function test_view_deleted_article()
    {
        $this->withoutVite();

        $article = Article::factory()->createPostWithRegisteredPerson()->withRevision()->deleted()->create();

        $response = $this->get(route('blog.single', ['article' => $article]));

        $response->assertNotFound();
    }

    /**
     * Tests previewing a draft article.
     *
     * @return void
     */
    public function test_preview_draft_article()
    {
        $this->withoutVite();

        $article = Article::factory()->createPostWithRegisteredPerson()->withRevision()->create();

        $response = $this->get($article->presenter()->privateUrl());

        $response->assertSuccessful();
    }

    /**
     * Tests previewing a published article.
     *
     * @return void
     */
    public function test_preview_published_article()
    {
        $this->withoutVite();

        $article = Article::factory()->createPostWithRegisteredPerson()->withRevision()->published()->create();

        $response = $this->get($article->presenter()->privateUrl());

        $response->assertSuccessful();
    }

    /**
     * Tests previewing a scheduled article.
     *
     * @return void
     */
    public function test_preview_scheduled_article()
    {
        $this->withoutVite();

        $article = Article::factory()->createPostWithRegisteredPerson()->withRevision()->published($this->faker->dateTimeBetween('+1 week', '+3 weeks'))->create();

        $response = $this->get($article->presenter()->privateUrl());

        $response->assertSuccessful();
    }

    /**
     * Tests previewing a deleted article.
     *
     * @return void
     */
    public function test_preview_deleted_article()
    {
        $this->withoutVite();

        $article = Article::factory()->createPostWithRegisteredPerson()->withRevision()->deleted()->create();

        $response = $this->get($article->presenter()->privateUrl());

        $response->assertSuccessful();
    }
}
