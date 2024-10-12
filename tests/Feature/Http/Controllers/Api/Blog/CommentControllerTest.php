<?php

namespace Tests\Feature\Http\Controllers\Api\Blog;

use App\Events\Comments\CommentRemoved;
use App\Events\Comments\CommentStatusChanged;
use App\Events\Comments\CommentUpdated;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Person;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\Fakes\CommentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Traits\CreatesUser;
use Tests\Feature\Traits\InteractsWithJWT;
use Tests\Feature\Traits\SeedsWith;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use CreatesUser;
    use InteractsWithJWT;
    use RefreshDatabase;
    use SeedsWith;
    use WithFaker;

    /**
     * Tests getting all comments
     */
    #[Test]
    public function getting_all_comments()
    {
        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();
        $this->seedWith(CommentSeeder::class, ['article' => $article]);

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.index'));
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data' => [['id', 'comment']], 'meta', 'links']);

        $this->assertNotEmpty($response->json('data'));
    }

    /**
     * Tests getting all comments with article
     */
    #[Test]
    public function getting_all_comments_with_article()
    {
        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();
        $this->seedWith(CommentSeeder::class, ['article' => $article]);

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.index', ['article' => $article]));
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data' => [['id', 'comment']], 'meta', 'links'])
            ->assertJsonPath('data.*.article.id', array_fill(0, count($response->json('data')), $article->getKey()));

        $this->assertNotEmpty($response->json('data'));
    }

    /**
     * Tests getting all comments with user
     */
    #[Test]
    public function getting_all_comments_with_user()
    {
        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();

        Comment::factory()->registered($this->user)->for($article)->create();

        $params = [
            'user' => $this->user,
        ];

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.index', $params));
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data' => [['id', 'comment']], 'meta', 'links'])
            ->assertJson(['data' => [
                [
                    'post' => [
                        'person' => [
                            'user_id' => $this->user->getKey(),
                        ],
                    ],
                ],
            ]]);

        $this->assertNotEmpty($response->json('data'));
    }

    /**
     * Tests getting all comments with article and user
     */
    #[Test]
    public function getting_all_comments_with_article_user()
    {
        [$article1, $article2] = Article::factory(2)->withRevision()->createPostWithRegisteredPerson()->create();

        Comment::factory()->registered($this->user)->for($article1)->create();
        Comment::factory()->registered($this->user)->for($article2)->create();

        $params = [
            'article' => $article1,
            'user' => $this->user,
        ];

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.index', $params));
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data' => [['id', 'comment']], 'meta', 'links'])
            ->assertJson(['data' => [
                [
                    'article' => ['id' => $article1->getKey()],
                    'post' => [
                        'person' => ['user_id' => $this->user->getKey()],
                    ],
                ],
            ]]);

        $this->assertNotEmpty($response->json('data'));
    }

    /**
     * Tests getting all comments matching person name
     */
    #[Test]
    public function getting_all_comments_with_person_name()
    {
        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();

        $info = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        Comment::factory()->createPostWithPerson(Person::factory($info))->for($article)->create();

        $params = [
            'article' => $article,
            'commenter' => [
                'name' => $info['name'],
            ],
        ];

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.index', $params));

        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data' => [['id', 'comment', 'commenter' => ['name', 'email']]]])
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    ['commenter' => $info],
                ],
            ]);

        //$this->assertNotEmpty($response->json('data'));
    }

    /**
     * Tests getting all comments partially matching commenter name
     */
    #[Test]
    public function getting_all_comments_with_commenter_partial_name()
    {
        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();

        $info = [
            'name' => 'Joe Blow',
            'email' => $this->faker->email,
        ];

        $comment = Comment::factory()->createPostWithPerson(Person::factory($info))->for($article)->create();

        $params = [
            'article' => $article,
            'commenter' => [
                'name' => 'blo',
            ],
        ];

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.index', $params));

        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data' => [['id', 'comment', 'commenter' => ['name', 'email']]]])
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    ['commenter' => $info],
                ],
            ]);

        //$this->assertNotEmpty($response->json('data'));
    }

    /**
     * Tests getting all comments matching commenter email
     */
    #[Test]
    public function getting_all_comments_with_commenter_email()
    {
        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();

        $info = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        Comment::factory()->createPostWithPerson(Person::factory($info))->for($article)->create();

        $params = [
            'article' => $article,
            'commenter' => [
                'email' => $info['email'],
            ],
        ];

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.index', $params));

        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data' => [['id', 'comment', 'commenter' => ['name', 'email']]]])
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    ['commenter' => $info],
                ],
            ]);
    }

    /**
     * Tests getting all comments partially matching commenter email
     */
    #[Test]
    public function getting_all_comments_with_commenter_partial_email()
    {
        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();

        $info = [
            'name' => $this->faker->name,
            'email' => 'joe.blow@gmail.com',
        ];

        Comment::factory(5)->createPostWithPerson(Person::factory($info))->for($article)->create();

        $params = [
            'article' => $article,
            'commenter' => [
                'email' => 'blow',
            ],
        ];

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.index', $params));

        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data' => [['id', 'comment', 'commenter' => ['name', 'email']]]])
            ->assertJsonCount(5, 'data')
            ->assertJson([
                'data' => [
                    ['commenter' => $info],
                ],
            ]);
    }

    /**
     * Tests not find comments with commenter
     */
    #[Test]
    public function getting_all_comments_with_commenter_not_found()
    {
        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();

        $info = [
            'name' => $this->faker->name,
            'email' => 'joe.blow@gmail.com',
        ];

        Comment::factory(5)->createPostWithPerson(Person::factory($info))->for($article)->create();

        $params = [
            'article' => $article,
            'commenter' => [
                'email' => 'john',
            ],
        ];

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.index', $params));

        $response
            ->assertSuccessful()
            ->assertJsonCount(0, 'data');
    }

    /**
     * Tests getting comments awaiting verification
     */
    #[Test]
    public function getting_awaiting_verification_comments()
    {
        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();

        Comment::factory(5)->guest()->for($article)->create();

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.index', ['show' => 'awaiting_verification']));
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(5, 'data');

        $this->assertCount(5, array_filter($response->json('data'), fn (array $comment) => $comment['status'] === 'awaiting_verification'));
    }

    /**
     * Tests getting comments awaiting approval
     */
    #[Test]
    public function getting_awaiting_approval_comments()
    {
        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();

        Comment::factory(5)->registered()->awaitingApproval(User::factory())->for($article)->create();

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.index', ['show' => 'awaiting_approval']));
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(5, 'data');

        $this->assertCount(5, array_filter($response->json('data'), fn (array $comment) => $comment['status'] === 'awaiting_approval'));
    }

    /**
     * Tests getting approved comments
     */
    #[Test]
    public function getting_approved_comments()
    {
        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();

        Comment::factory(5)->guest()->registered()->approved(User::factory())->for($article)->create();

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.index', ['show' => 'approved']));
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(5, 'data');

        $this->assertCount(5, array_filter($response->json('data'), fn (array $comment) => $comment['status'] === 'approved'));
    }

    /**
     * Tests getting denied comments
     */
    #[Test]
    public function getting_denied_comments()
    {
        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();

        Comment::factory(5)->guest()->denied()->approved(User::factory())->for($article)->create();

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.index', ['show' => 'denied']));
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(5, 'data');

        $this->assertCount(5, array_filter($response->json('data'), fn (array $comment) => $comment['status'] === 'denied'));
    }

    /**
     * Tests getting a comment with a registered user
     */
    #[Test]
    public function getting_comment_with_registered_user()
    {
        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();

        $comment = Comment::factory()->registered($this->user)->for($article)->create();

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.show', ['comment' => $comment]));
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'comment', 'post' => ['person' => ['user_id']]])
            ->assertJson($comment->toArray())
            ->assertJson([
                'user_type' => 'registered',
                'post' => [
                    'person' => ['user_id' => $this->user->getKey()],
                ],
            ]);
    }

    /**
     * Tests getting a comment with a guest user
     */
    #[Test]
    public function getting_comment_with_guest_user()
    {
        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();

        $comment = Comment::factory()->guest()->for($article)->create();

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.show', ['comment' => $comment]));
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'comment', 'commenter' => ['name', 'email']])
            ->assertJson($comment->toArray())
            ->assertJson([
                'user_type' => 'guest',
                'post' => [
                    'person' => ['user_id' => null],
                ],
            ]);

        $this->assertIsString($response->json('commenter.name'));
        $this->assertIsString($response->json('commenter.email'));
    }

    /**
     * Tests getting a comment with a guest user
     */
    #[Test]
    public function getting_comment_with_guest_user_verified()
    {
        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();

        $comment = Comment::factory()->verifiedGuest()->for($article)->create();

        $response = $this->actingAs($this->admin)->getJson(route('api.comments.show', ['comment' => $comment]));
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'id',
                'comment',
                'post' => [
                    'person' => ['name', 'email', 'email_verified_at'],
                ],
            ])
            ->assertJson($comment->toArray())
            ->assertJson([
                'user_type' => 'guest',
                'post' => [
                    'person' => ['user_id' => null],
                ],
            ]);

        $this->assertIsString($response->json('post.person.name'));
        $this->assertIsString($response->json('post.person.email'));
        $this->assertIsString($response->json('post.person.email_verified_at'));
    }

    /**
     * Tests updating comment title
     */
    #[Test]
    public function updating_comment_title()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();
        $comment = Comment::factory()->verifiedGuest()->for($article)->create();

        $data = [
            'title' => $this->faker->text,
        ];

        $response = $this->actingAs($this->admin)->putJson(route('api.comments.update', ['comment' => $comment]), $data);
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'title', 'comment'])
            ->assertJson(['title' => $data['title']]);

        Event::assertDispatched(CommentUpdated::class);
        Event::assertNotDispatched(CommentStatusChanged::class);
    }

    /**
     * Tests updating comment content
     */
    #[Test]
    public function updating_comment_content()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();
        $comment = Comment::factory()->verifiedGuest()->for($article)->create();

        $data = [
            'comment' => $this->faker->text,
        ];

        $response = $this->actingAs($this->admin)->putJson(route('api.comments.update', ['comment' => $comment]), $data);
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'title', 'comment'])
            ->assertJson(['comment' => $data['comment']]);

        Event::assertDispatched(CommentUpdated::class);
        Event::assertNotDispatched(CommentStatusChanged::class);
    }

    /**
     * Tests updating comment status to awaiting_verification
     */
    #[Test]
    public function updating_comment_status_awaiting_verification()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();
        $comment = Comment::factory()->verifiedGuest()->for($article)->create();

        $data = [
            'status' => 'awaiting_verification',
        ];

        $response = $this->actingAs($this->admin)->putJson(route('api.comments.update', ['comment' => $comment]), $data);
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'comment', 'status', 'marked_by'])
            ->assertJson([
                'status' => $data['status'],
                'marked_by' => $this->admin->toArray(),
            ]);

        Event::assertNotDispatched(CommentUpdated::class);
        Event::assertDispatched(CommentStatusChanged::class);
    }

    /**
     * Tests updating comment status to awaiting_approval from guest comment (awaiting verification)
     */
    #[Test]
    public function updating_comment_status_awaiting_approval()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();
        $comment = Comment::factory()->guest()->for($article)->create();

        $data = [
            'status' => 'awaiting_approval',
        ];

        $response = $this->actingAs($this->admin)->putJson(route('api.comments.update', ['comment' => $comment]), $data);
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'comment', 'status', 'marked_by'])
            ->assertJson([
                'status' => $data['status'],
                'marked_by' => $this->admin->toArray(),
            ]);

        Event::assertNotDispatched(CommentUpdated::class);
        Event::assertDispatched(CommentStatusChanged::class);
    }

    /**
     * Tests updating comment status to denied
     */
    #[Test]
    public function updating_comment_status_denied()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();
        $comment = Comment::factory()->verifiedGuest()->for($article)->create();

        $data = [
            'status' => 'denied',
        ];

        $response = $this->actingAs($this->admin)->putJson(route('api.comments.update', ['comment' => $comment]), $data);
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'comment', 'status', 'marked_by'])
            ->assertJson([
                'status' => $data['status'],
                'marked_by' => $this->admin->toArray(),
            ]);

        Event::assertNotDispatched(CommentUpdated::class);
        Event::assertDispatched(CommentStatusChanged::class);
    }

    /**
     * Tests updating comment status to approved
     */
    #[Test]
    public function updating_comment_status_approved()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();
        $comment = Comment::factory()->verifiedGuest()->for($article)->create();

        $data = [
            'status' => 'approved',
        ];

        $response = $this->actingAs($this->admin)->putJson(route('api.comments.update', ['comment' => $comment]), $data);
        $response
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'comment', 'status', 'marked_by'])
            ->assertJson([
                'status' => $data['status'],
                'marked_by' => $this->admin->toArray(),
            ]);

        Event::assertNotDispatched(CommentUpdated::class);
        Event::assertDispatched(CommentStatusChanged::class);
    }

    /**
     * Tests updating comment status to unknown
     */
    #[Test]
    public function updating_comment_status_unknown()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();
        $comment = Comment::factory()->verifiedGuest()->for($article)->create();

        $data = [
            'status' => 'unknown',
        ];

        $response = $this->actingAs($this->admin)->putJson(route('api.comments.update', ['comment' => $comment]), $data);
        $response->assertInvalid(['status']);

        Event::assertNotDispatched(CommentUpdated::class);
        Event::assertNotDispatched(CommentStatusChanged::class);
    }

    /**
     * Tests updating comment status to invalid
     */
    #[Test]
    public function updating_comment_status_invalid()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();
        $comment = Comment::factory()->verifiedGuest()->for($article)->create();

        $data = [
            'status' => 'invalid',
        ];

        $response = $this->actingAs($this->admin)->putJson(route('api.comments.update', ['comment' => $comment]), $data);
        $response->assertInvalid(['status']);

        Event::assertNotDispatched(CommentUpdated::class);
        Event::assertNotDispatched(CommentStatusChanged::class);
    }

    /**
     * Tests deleting active registered comment
     */
    #[Test]
    public function deleting_active_registered_comment()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();
        $comment = Comment::factory()->registered()->for($article)->create();

        $response = $this->actingAs($this->admin)->deleteJson(route('api.comments.destroy', ['comment' => $comment]));
        $response->assertSuccessful();

        Event::assertDispatched(CommentRemoved::class);

        $this->assertTrue($comment->refresh()->post->trashed());
    }

    /**
     * Tests deleting active guest comment
     */
    #[Test]
    public function deleting_active_guest_comment()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();
        $comment = Comment::factory()->verifiedGuest()->for($article)->create();

        $response = $this->actingAs($this->admin)->deleteJson(route('api.comments.destroy', ['comment' => $comment]));
        $response->assertSuccessful();

        Event::assertDispatched(CommentRemoved::class);

        $this->assertTrue($comment->refresh()->post->trashed());
    }

    /**
     * Tests deleting active comment
     */
    #[Test]
    public function deleting_inactive_comment()
    {
        Event::fake();

        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->create();
        $comment = Comment::factory()->createPostWithRegisteredPerson(postFactory: Post::factory()->deleted())->for($article)->create();

        $response = $this->actingAs($this->admin)->deleteJson(route('api.comments.destroy', ['comment' => $comment]));
        $response->assertSuccessful();

        Event::assertDispatched(CommentRemoved::class);

        $this->assertTrue($comment->refresh()->post->trashed());
    }

    /**
     * {@inheritDoc}
     */
    protected function afterRefreshingDatabase()
    {
        // Clear default comments
        Comment::truncate();
    }
}
