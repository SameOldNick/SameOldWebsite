<?php

namespace Tests\Feature\Main\Blog;

use App\Components\Settings\Facades\PageSettings;
use App\Enums\CommentStatus;
use App\Models\Article;
use App\Models\Comment;
use Biscolab\ReCaptcha\Facades\ReCaptcha;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Traits\CreatesUser;
use Tests\Feature\Traits\FakesReCaptcha;
use Tests\TestCase;

class BlogCommentTest extends TestCase
{
    use CreatesUser;
    use FakesReCaptcha;
    use RefreshDatabase;
    use WithFaker;

    /**
     * @test
     */
    #[Test]
    public function registered_user_can_post_comment()
    {
        PageSettings::fake('blog', [
            'user_authentication' => 'registered',
            'comment_moderation' => 'disabled',
            'use_captcha' => 'disabled',
        ]);

        $article = Article::factory()->hasPostWithUser()->published()->create();

        $response = $this->actingAs($this->user)->post(route('blog.comment', ['article' => $article]), [
            'comment' => $this->faker->realText(),
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::owned($this->user)->first();
        $this->assertNotNull($comment);
        $this->assertEquals($this->user->email, $comment->commenter_info['email']);
        $this->assertEquals(CommentStatus::Approved->value, $comment->status);
    }

    /**
     * @test
     */
    #[Test]
    public function comments_are_automatically_moderated()
    {
        // Assuming a setting to enable auto-approval
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_unverified',
            'comment_moderation' => 'auto',
            'use_captcha' => 'disabled',
        ]);

        $article = Article::factory()->hasPostWithUser()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is the comment.',
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withEmail($email)->first();
        $this->assertNotNull($comment);
        $this->assertEquals(CommentStatus::Approved->value, $comment->status);
    }

    /**
     * @test
     */
    #[Test]
    public function comments_are_pending_approval()
    {
        // Assuming a setting to disable auto-approval
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_unverified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'disabled',
        ]);

        $article = Article::factory()->hasPostWithUser()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is the comment.',
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withEmail($email)->first();
        $this->assertNotNull($comment);
        $this->assertEquals(CommentStatus::AwaitingApproval->value, $comment->status);
    }

    /**
     * @test
     */
    #[Test]
    public function comments_arent_moderated()
    {
        // Assuming a setting to enable auto-approval
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_unverified',
            'comment_moderation' => 'disabled',
            'use_captcha' => 'disabled',
        ]);

        $article = Article::factory()->hasPostWithUser()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is the comment.',
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withEmail($email)->first();
        $this->assertNotNull($comment);
        $this->assertEquals(CommentStatus::Approved->value, $comment->status);
    }

    /**
     * @test
     */
    #[Test]
    public function guest_must_pass_captcha_to_submit_comment()
    {
        ReCaptcha::fake();

        // Simulate CAPTCHA validation logic here
        // For testing, assume CAPTCHA is valid
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'guest',
        ]);

        $article = Article::factory()->withRevision()->hasPostWithUser()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is a test comment',
            'name' => $name,
            'email' => $email,
            recaptchaFieldName() => ReCaptcha::validResponse(),
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $this->assertNotNull(Comment::withEmail($email)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function post_comment_missing_comment_field()
    {
        ReCaptcha::fake();
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'guest',
        ]);

        $article = Article::factory()->withRevision()->hasPostWithUser()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->fromRoute('blog.single', ['article' => $article])->post(route('blog.comment', ['article' => $article]), [
            'name' => $name,
            'email' => $email,
            recaptchaFieldName() => ReCaptcha::validResponse(),
        ]);

        $response->assertInvalid(['comment']);

        $this->assertNull(Comment::withEmail($email)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function post_comment_invalid_recaptcha()
    {
        ReCaptcha::fake();
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'guest',
        ]);

        $article = Article::factory()->withRevision()->hasPostWithUser()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is a test comment',
            'name' => $name,
            'email' => $email,
            recaptchaFieldName() => $this->faker->uuid,
        ]);

        $response->assertInvalid([recaptchaFieldName()]);

        $this->assertNull(Comment::withEmail($email)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function registered_user_does_not_need_captcha_to_submit_comment()
    {
        ReCaptcha::fake();
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'guest',
        ]);

        $article = Article::factory()->withRevision()->hasPostWithUser()->published()->create();

        $response = $this->actingAs($this->user)->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is a test comment',
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::owned($this->user)->first();
        $this->assertNotNull($comment);
    }

    /**
     * @test
     */
    #[Test]
    public function all_users_must_pass_captcha_to_submit_comment_as_guest()
    {
        ReCaptcha::fake();
        // Simulate CAPTCHA validation logic here
        // For testing, assume CAPTCHA is valid
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'all',
        ]);

        $article = Article::factory()->withRevision()->hasPostWithUser()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is a test comment',
            'name' => $name,
            'email' => $email,
            recaptchaFieldName() => ReCaptcha::validResponse(),
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $this->assertNotNull(Comment::withEmail($email)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function all_users_must_pass_captcha_to_submit_comment_as_registered_user()
    {
        ReCaptcha::fake();
        // Simulate CAPTCHA validation logic here
        // For testing, assume CAPTCHA is valid
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'all',
        ]);

        $article = Article::factory()->withRevision()->hasPostWithUser()->published()->create();

        $response = $this->actingAs($this->user)->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is a test comment',
            recaptchaFieldName() => ReCaptcha::validResponse(),
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $this->assertNotNull(Comment::owned($this->user)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function all_users_dont_pass_captcha_to_submit_comment_as_guest_user()
    {
        ReCaptcha::fake();
        // Simulate CAPTCHA validation logic here
        // For testing, assume CAPTCHA is valid
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'all',
        ]);

        [$name, $email] = [$this->faker->name, $this->faker->email];
        $article = Article::factory()->withRevision()->hasPostWithUser()->published()->create();

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is a test comment',
            'name' => $name,
            'email' => $email,
            recaptchaFieldName() => $this->faker->uuid,
        ]);

        $response
            ->assertRedirect() // Assuming redirect after submission
            ->assertInvalid([recaptchaFieldName()]);

        $this->assertNull(Comment::withEmail($email)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function all_users_dont_pass_captcha_to_submit_comment_as_registered_user()
    {
        ReCaptcha::fake();
        // Simulate CAPTCHA validation logic here
        // For testing, assume CAPTCHA is valid
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'all',
        ]);

        $article = Article::factory()->withRevision()->hasPostWithUser()->published()->create();

        $response = $this->actingAs($this->user)->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is a test comment',
            recaptchaFieldName() => $this->faker->uuid,
        ]);

        $response
            ->assertRedirect() // Assuming redirect after submission
            ->assertInvalid([recaptchaFieldName()]);

        $this->assertNull(Comment::owned($this->user)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function guest_can_submit_comment_without_captcha_when_disabled()
    {
        ReCaptcha::fake();
        // Simulate CAPTCHA validation logic here
        // For testing, assume CAPTCHA is valid
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'disabled',
        ]);

        $article = Article::factory()->withRevision()->hasPostWithUser()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is a test comment',
            'name' => $name,
            'email' => $email,
            recaptchaFieldName() => ReCaptcha::validResponse(),
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $this->assertNotNull(Comment::withEmail($email)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function registered_user_can_submit_comment_without_captcha_when_disabled()
    {
        // Simulate CAPTCHA validation logic here
        // For testing, assume CAPTCHA is valid
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'disabled',
        ]);

        $article = Article::factory()->withRevision()->hasPostWithUser()->published()->create();

        $response = $this->actingAs($this->user)->post(route('blog.comment', ['article' => $article]), [
            'comment' => 'This is a test comment',
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $this->assertNotNull(Comment::owned($this->user)->first());
    }
}
