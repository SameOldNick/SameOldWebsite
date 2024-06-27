<?php

namespace Tests\Feature\Http\Controllers\Api\Blog;

use App\Events\Articles\ArticleCreated;
use App\Events\Articles\ArticleDeleted;
use App\Events\Articles\ArticlePublished;
use App\Events\Articles\ArticleRestored;
use App\Events\Articles\ArticleRevisionUpdated;
use App\Events\Articles\ArticleScheduled;
use App\Models\Article;
use App\Models\Image;
use App\Models\Revision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Traits\CreatesUser;
use Tests\Feature\Traits\InteractsWithJWT;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ArticleControllerTest extends TestCase
{
    use CreatesUser;
    use InteractsWithJWT;
    use RefreshDatabase;
    use WithFaker;

    /**
     * Tests getting all articles
     */
    #[Test]
    public function getting_all_articles()
    {
        Article::factory(5)->withRevision()->hasPostWithUser()->published()->create();
        Article::factory(5)->withRevision()->hasPostWithUser()->scheduled()->create();
        Article::factory(5)->withRevision()->hasPostWithUser()->deleted()->create();

        $response = $this->actingAs($this->admin)->getJson(route('api.articles.index'));
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(15, 'data');
    }

    /**
     * Tests getting all unpublished articles
     */
    #[Test]
    public function getting_unpublished_articles()
    {
        Article::factory(5)->withRevision()->hasPostWithUser()->create();
        Article::factory(5)->withRevision()->hasPostWithUser()->published()->create();
        Article::factory(5)->withRevision()->hasPostWithUser()->scheduled()->create();
        Article::factory(5)->withRevision()->hasPostWithUser()->deleted()->create();

        $response = $this->actingAs($this->admin)->getJson(route('api.articles.index', ['show' => 'unpublished']));
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(5, 'data');

        // Test all articles are unpublished.
        $this->assertCount(5, array_filter($response->json('data.*.published_at'), fn ($dateTime) => is_null($dateTime)));
    }

    /**
     * Tests getting all published articles
     */
    #[Test]
    public function getting_published_articles()
    {
        Article::factory(5)->withRevision()->hasPostWithUser()->create();
        Article::factory(5)->withRevision()->hasPostWithUser()->published()->create();
        Article::factory(5)->withRevision()->hasPostWithUser()->scheduled()->create();
        Article::factory(5)->withRevision()->hasPostWithUser()->deleted()->create();

        $response = $this->actingAs($this->admin)->getJson(route('api.articles.index', ['show' => 'published']));
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(5, 'data');

        // Test all articles are published.
        $this->assertCount(5, array_filter($response->json('data.*.published_at'), fn ($dateTime) => now()->isAfter($dateTime)));
    }

    /**
     * Tests getting all scheduled articles
     */
    #[Test]
    public function getting_scheduled_articles()
    {
        Article::factory(5)->withRevision()->hasPostWithUser()->create();
        Article::factory(5)->withRevision()->hasPostWithUser()->published()->create();
        Article::factory(5)->withRevision()->hasPostWithUser()->scheduled()->create();
        Article::factory(5)->withRevision()->hasPostWithUser()->deleted()->create();

        $response = $this->actingAs($this->admin)->getJson(route('api.articles.index', ['show' => 'scheduled']));
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(5, 'data');

        // Test all articles are scheduled for the future.
        $this->assertCount(5, array_filter($response->json('data.*.published_at'), fn ($dateTime) => now()->isBefore($dateTime)));
    }

    /**
     * Tests getting all removed articles
     */
    #[Test]
    public function getting_removed_articles()
    {
        Article::factory(5)->withRevision()->hasPostWithUser()->create();
        Article::factory(5)->withRevision()->hasPostWithUser()->published()->create();
        Article::factory(5)->withRevision()->hasPostWithUser()->scheduled()->create();
        Article::factory(5)->withRevision()->hasPostWithUser()->deleted()->create();

        $response = $this->actingAs($this->admin)->getJson(route('api.articles.index', ['show' => 'removed']));
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(5, 'data');

        // Test all articles are deleted.
        $this->assertCount(5, array_filter($response->json('data.*.deleted_at')));
    }

    /**
     * Tests getting an article
     */
    #[Test]
    public function getting_single_article()
    {
        $article = Article::factory()->withRevision()->hasPostWithUser()->published()->create();

        $response = $this->actingAs($this->admin)->getJson("/api/blog/articles/{$article->id}");
        $response
            ->assertSuccessful()
            ->assertJsonStructure(array_keys($article->toArray()));
    }

    /**
     * Tests storing published article
     */
    #[Test]
    public function storing_published_article()
    {
        Event::fake();

        $data = [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'published_at' => now()->toDateTimeString(),
            'revision' => [
                'content' => 'Test content',
                'summary' => 'Test summary',
            ],
        ];

        $response = $this->actingAs($this->admin)->postJson(route('api.articles.store'), $data);

        $response->assertCreated();

        $article = Article::firstWhere(['title' => 'Test Article']);

        $this->assertNotNull($article);
        // Test article has associated user
        $this->assertNotNull($article->post->user);
        // Test the article is published.
        $this->assertTrue($article->is_published);

        Event::assertDispatched(ArticleCreated::class);
        Event::assertDispatched(ArticlePublished::class);
    }

    /**
     * Tests updating existing article
     */
    #[Test]
    public function updating_existing_article()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->hasPostWithUser()->create();
        $data = [
            'title' => 'Updated Title',
            'slug' => 'updated-title',
            'published_at' => now()->addDay()->toDateTimeString(),
        ];

        $response = $this->actingAs($this->admin)->putJson(route('api.articles.update', ['article' => $article]), $data);

        $response->assertSuccessful();
        $this->assertDatabaseHas('articles', ['title' => 'Updated Title']);

        Event::assertDispatched(ArticleScheduled::class);
    }

    /**
     * Tests changing current revision for associated article
     */
    #[Test]
    public function changing_associated_revision_to_existing_article()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->hasPostWithUser()->create();
        $revision = Revision::factory()->create(['article_id' => $article->id]);

        $data = ['revision' => $revision->uuid];
        $response = $this->actingAs($this->admin)->postJson(route('api.articles.revisions.revision', ['article' => $article]), $data);

        $response->assertSuccessful();

        $this->assertEquals($revision->id, $article->fresh()->current_revision_id);

        Event::assertDispatched(ArticleRevisionUpdated::class);
    }

    /**
     * Tests changing current revision for unassociated article
     */
    #[Test]
    public function adding_unassociated_revision_to_existing_article()
    {
        Event::fake();

        [$article1, $article2] = Article::factory(2)->withRevision()->hasPostWithUser()->create();
        $revision = Revision::factory()->create(['article_id' => $article2->id]);

        $data = ['revision' => $revision->uuid];
        $response = $this->actingAs($this->admin)->postJson(route('api.articles.revisions.revision', ['article' => $article1]), $data);

        $response->assertInvalid(['revision']);
        $this->assertNotEquals($revision->getKey(), $article1->fresh()->revision->getKey());
        Event::assertNotDispatched(ArticleRevisionUpdated::class);
    }

    /**
     * Tests restoring deleted article
     */
    #[Test]
    public function restoring_deleted_article()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->hasPostWithUser()->deleted()->create();

        $response = $this->actingAs($this->admin)->postJson(route('api.articles.restore', ['article' => $article]));

        $response->assertSuccessful();

        $this->assertNotNull($article->refresh());
        $this->assertNotSoftDeleted($article);

        Event::assertDispatched(ArticleRestored::class);
    }

    /**
     * Tests restoring restored article
     */
    #[Test]
    public function restoring_restored_article()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->hasPostWithUser()->create();

        $response = $this->actingAs($this->admin)->postJson(route('api.articles.restore', ['article' => $article]));

        $response
            ->assertConflict()
            ->assertJson([
                'error' => __('Article ":title" is already restored.', ['title' => $article->title])
            ]);

        $this->assertNotNull($article->refresh());
        $this->assertNotSoftDeleted($article);

        Event::assertNotDispatched(ArticleRestored::class);
    }

    /**
     * Tests destroying existing active article
     */
    #[Test]
    public function destroying_existing_article()
    {
        // The post won't be marked as deleted if event is caught by event faker.
        Event::fakeExcept(sprintf('eloquent.deleting: %s', Article::class));

        $article = Article::factory()->withRevision()->hasPostWithUser()->create();

        $response = $this->actingAs($this->admin)->deleteJson(route('api.articles.destroy', ['article' => $article]));

        $response->assertSuccessful();

        $this
            ->assertSoftDeleted($article->refresh())
            ->assertSoftDeleted($article->post->refresh());

        Event::assertDispatched(ArticleDeleted::class);
    }

    /**
     * Tests destryoing existing trashed article
     */
    #[Test]
    public function destroying_trashed_article()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->hasPostWithUser()->deleted()->create();

        $response = $this->actingAs($this->admin)->deleteJson(route('api.articles.destroy', ['article' => $article]));

        $response->assertNotFound();

        $this->assertSoftDeleted($article->refresh());

        Event::assertNotDispatched(ArticleDeleted::class);
    }
}
