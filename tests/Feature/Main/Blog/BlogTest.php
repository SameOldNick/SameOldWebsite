<?php

namespace Tests\Feature\Main\Blog;

use App\Events\Comments\CommentCreated;
use App\Events\Comments\CommentApproved;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Revision;
use App\Models\User;
use App\Notifications\CommentPosted;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
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
    public function testViewDraftArticle() {
        $this->withoutVite();

        $article = Article::factory()->hasPostWithUser()->withRevision()->create();

        $response = $this->get(route('blog.single', ['article' => $article]));

        $response->assertNotFound();
    }

    /**
     * Tests viewing a published article.
     *
     * @return void
     */
    public function testViewPublishedArticle() {
        $this->withoutVite();

        $article = Article::factory()->hasPostWithUser()->withRevision()->published()->create();

        $response = $this->get(route('blog.single', ['article' => $article]));

        $response->assertSuccessful();
    }

    /**
     * Tests viewing a scheduled article.
     *
     * @return void
     */
    public function testViewScheduledArticle() {
        $this->withoutVite();

        $article = Article::factory()->hasPostWithUser()->withRevision()->published($this->faker->dateTimeBetween('+1 week', '+3 weeks'))->create();

        $response = $this->get(route('blog.single', ['article' => $article]));

        $response->assertNotFound();
    }

    /**
     * Tests a scheduled article is viewable after date.
     *
     * @return void
     */
    public function testViewScheduledArticleInFuture() {
        $this->withoutVite();

        $published = Carbon::instance($this->faker->dateTimeBetween('+1 week', '+3 weeks'));

        $article = Article::factory()->hasPostWithUser()->withRevision()->published($published)->create();

        $this->travelTo($published->addMinute(1));

        $response = $this->get(route('blog.single', ['article' => $article]));

        $response->assertSuccessful();
    }

    /**
     * Tests a deleted article is not publically viewable.
     *
     * @return void
     */
    public function testViewDeletedArticle() {
        $this->withoutVite();

        $article = Article::factory()->hasPostWithUser()->withRevision()->deleted()->create();

        $response = $this->get(route('blog.single', ['article' => $article]));

        $response->assertNotFound();
    }

    /**
     * Tests previewing a draft article.
     *
     * @return void
     */
    public function testPreviewDraftArticle() {
        $this->withoutVite();

        $article = Article::factory()->hasPostWithUser()->withRevision()->create();

        $response = $this->get($article->createPrivateUrl());

        $response->assertSuccessful();
    }

    /**
     * Tests previewing a published article.
     *
     * @return void
     */
    public function testPreviewPublishedArticle() {
        $this->withoutVite();

        $article = Article::factory()->hasPostWithUser()->withRevision()->published()->create();

        $response = $this->get($article->createPrivateUrl());

        $response->assertSuccessful();
    }

    /**
     * Tests previewing a scheduled article.
     *
     * @return void
     */
    public function testPreviewScheduledArticle() {
        $this->withoutVite();

        $article = Article::factory()->hasPostWithUser()->withRevision()->published($this->faker->dateTimeBetween('+1 week', '+3 weeks'))->create();

        $response = $this->get($article->createPrivateUrl());

        $response->assertSuccessful();
    }

    /**
     * Tests previewing a deleted article.
     *
     * @return void
     */
    public function testPreviewDeletedArticle() {
        $this->withoutVite();

        $article = Article::factory()->hasPostWithUser()->withRevision()->deleted()->create();

        $response = $this->get($article->createPrivateUrl());

        $response->assertSuccessful();
    }
}
