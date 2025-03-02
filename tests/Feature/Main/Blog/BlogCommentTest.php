<?php

namespace Tests\Feature\Main\Blog;

use App\Components\Captcha\Drivers\Recaptcha\Testing\DriverBuilder;
use App\Components\Captcha\Facades\Captcha;
use App\Components\Captcha\Rules\CaptchaRule;
use App\Components\Settings\Facades\PageSettings;
use App\Enums\CommentStatus;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Traits\CreatesArticle;
use Tests\Feature\Traits\CreatesUser;
use Tests\TestCase;

class BlogCommentTest extends TestCase
{
    use CreatesArticle;
    use CreatesUser;
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

        $response = $this->actingAs($this->user)->post(route('blog.comment', ['article' => $this->article]), [
            'comment' => $this->faker->realText(),
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withPersonDetails('user', $this->user)->first();
        $this->assertNotNull($comment);
        $this->assertEquals($this->user->email, $comment->commenter['email']);
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

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $this->article]), [
            'comment' => 'This is the comment.',
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withPersonDetails('email', $email)->first();
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

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $this->article]), [
            'comment' => 'This is the comment.',
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withPersonDetails('email', $email)->first();
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

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $this->article]), [
            'comment' => 'This is the comment.',
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withPersonDetails('email', $email)->first();
        $this->assertNotNull($comment);
        $this->assertEquals(CommentStatus::Approved->value, $comment->status);
    }

    /**
     * @test
     */
    #[Test]
    public function guest_must_pass_captcha_to_submit_comment()
    {
        Captcha::fake(DriverBuilder::create());
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'guest',
        ]);

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $this->article]), [
            'comment' => 'This is a test comment',
            'name' => $name,
            'email' => $email,
            'g-recaptcha-response' => CaptchaRule::validResponse(),
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $this->assertNotNull(Comment::withPersonDetails('email', $email)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function post_comment_missing_comment_field()
    {
        Captcha::fake(DriverBuilder::create());
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'guest',
        ]);

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->fromRoute('blog.single', ['article' => $this->article])->post(route('blog.comment', ['article' => $this->article]), [
            'name' => $name,
            'email' => $email,
            'g-recaptcha-response' => CaptchaRule::validResponse(),
        ]);

        $response->assertInvalid(['comment']);

        $this->assertNull(Comment::withPersonDetails('email', $email)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function post_comment_invalid_recaptcha()
    {
        Captcha::fake(DriverBuilder::create());
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'guest',
        ]);

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $this->article]), [
            'comment' => 'This is a test comment',
            'name' => $name,
            'email' => $email,
            'g-recaptcha-response' => $this->faker->uuid,
        ]);

        $response->assertInvalid(['g-recaptcha-response']);

        $this->assertNull(Comment::withPersonDetails('email', $email)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function registered_user_does_not_need_captcha_to_submit_comment()
    {
        Captcha::fake(DriverBuilder::create());
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'guest',
        ]);

        $response = $this->actingAs($this->user)->post(route('blog.comment', ['article' => $this->article]), [
            'comment' => 'This is a test comment',
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withPersonDetails('user', $this->user)->first();
        $this->assertNotNull($comment);
    }

    /**
     * @test
     */
    #[Test]
    public function all_users_must_pass_captcha_to_submit_comment_as_guest()
    {
        Captcha::fake(DriverBuilder::create());
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'all',
        ]);

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $this->article]), [
            'comment' => 'This is a test comment',
            'name' => $name,
            'email' => $email,
            'g-recaptcha-response' => CaptchaRule::validResponse(),
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $this->assertNotNull(Comment::withPersonDetails('email', $email)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function all_users_must_pass_captcha_to_submit_comment_as_registered_user()
    {
        Captcha::fake(DriverBuilder::create());
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'all',
        ]);

        $response = $this->actingAs($this->user)->post(route('blog.comment', ['article' => $this->article]), [
            'comment' => 'This is a test comment',
            'g-recaptcha-response' => CaptchaRule::validResponse(),
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $this->assertNotNull(Comment::withPersonDetails('user', $this->user)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function all_users_dont_pass_captcha_to_submit_comment_as_guest_user()
    {
        Captcha::fake(DriverBuilder::create());
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'all',
        ]);

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $this->article]), [
            'comment' => 'This is a test comment',
            'name' => $name,
            'email' => $email,
            'g-recaptcha-response' => $this->faker->uuid,
        ]);

        $response
            ->assertRedirect() // Assuming redirect after submission
            ->assertInvalid(['g-recaptcha-response']);

        $this->assertNull(Comment::withPersonDetails('email', $email)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function all_users_dont_pass_captcha_to_submit_comment_as_registered_user()
    {
        Captcha::fake(DriverBuilder::create());
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'all',
        ]);

        $response = $this->actingAs($this->user)->post(route('blog.comment', ['article' => $this->article]), [
            'comment' => 'This is a test comment',
            'g-recaptcha-response' => $this->faker->uuid,
        ]);

        $response
            ->assertRedirect() // Assuming redirect after submission
            ->assertInvalid(['g-recaptcha-response']);

        $this->assertNull(Comment::withPersonDetails('user', $this->user)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function guest_can_submit_comment_without_captcha_when_disabled()
    {
        Captcha::fake(DriverBuilder::create());
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'disabled',
        ]);

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $this->article]), [
            'comment' => 'This is a test comment',
            'name' => $name,
            'email' => $email,
            'g-recaptcha-response' => CaptchaRule::validResponse(),
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $this->assertNotNull(Comment::withPersonDetails('email', $email)->first());
    }

    /**
     * @test
     */
    #[Test]
    public function registered_user_can_submit_comment_without_captcha_when_disabled()
    {
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'manual',
            'use_captcha' => 'disabled',
        ]);

        $response = $this->actingAs($this->user)->post(route('blog.comment', ['article' => $this->article]), [
            'comment' => 'This is a test comment',
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $this->assertNotNull(Comment::withPersonDetails('user', $this->user)->first());
    }
}
