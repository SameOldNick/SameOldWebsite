<?php

namespace Tests\Feature\Main\Blog;

use App\Components\Settings\Facades\PageSettings;
use App\Enums\CommentStatus;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Traits\FakesReCaptcha;
use Tests\TestCase;

class BlogCommentVerifyTest extends TestCase
{
    use FakesReCaptcha;
    use RefreshDatabase;
    use WithFaker;

    /**
     * Tests a guest can post comment with email verification
     */
    #[Test]
    public function guest_can_post_comment_with_email_verification()
    {
        Mail::fake();
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
            'comment_moderation' => 'disabled',
        ]);

        $article = Article::factory()->createPostWithRegisteredPerson()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => $this->faker->realText(),
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withPersonDetails('email', $email)->first();
        $this->assertNotNull($comment);
        $this->assertEquals(CommentStatus::AwaitingVerification->value, $comment->status);
        $this->assertFalse($comment->post->person->hasVerifiedEmail());

        Mail::assertSent(function (Mailable $mail) use ($email) {
            return $mail->hasTo($email) && is_string($mail->viewData['link']);
        });
    }

    /**
     * @test
     */
    #[Test]
    public function guest_can_verify_email()
    {
        Mail::fake();
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified',
        ]);

        $article = Article::factory()->createPostWithRegisteredPerson()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => $this->faker->realText(),
            'name' => $name,
            'email' => $email,
        ]);

        $mailable = Mail::sent(fn (Mailable $mail) => $mail->hasTo($email))->first();

        $this->assertNotNull($mailable);
        $this->assertArrayHasKey('link', $mailable->viewData);

        $response = $this->get($mailable->viewData['link']);

        $response->assertRedirect(); // Assuming redirect after verification

        $comment = Comment::withPersonDetails('email', $email)->first();

        $this->assertNotNull($comment);
        $this->assertNotNull($comment->post->person);
        $this->assertTrue($comment->post->person->hasVerifiedEmail());
    }

    /**
     * @test
     */
    #[Test]
    public function guest_can_post_comment_without_email_verification()
    {
        Mail::fake();
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_unverified',
        ]);

        $article = Article::factory()->createPostWithRegisteredPerson()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => $this->faker->realText(),
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withPersonDetails('email', $email)->first();
        $this->assertNotNull($comment);
        $this->assertNotEquals(CommentStatus::AwaitingVerification->value, $comment->status);
        $this->assertTrue($comment->post->person->hasVerifiedEmail());

        Mail::assertNotSent(function (Mailable $mail) use ($email) {
            return $mail->hasTo($email);
        });
    }
}
