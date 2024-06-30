<?php

namespace Tests\Feature\Main\Blog;

use App\Components\Settings\Facades\PageSettings;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Traits\FakesReCaptcha;

class BlogCommentVerifyTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use FakesReCaptcha;

    /**
     * Tests a guest can post comment with email verification
     */
    #[Test]
    public function guest_can_post_comment_with_email_verification()
    {
        Mail::fake();
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_verified'
        ]);

        $article = Article::factory()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => $this->faker->realText(),
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withEmail($email)->first();
        $this->assertNotNull($comment);
        $this->assertEquals(Comment::STATUS_AWAITING_VERIFICATION, $comment->status);
        $this->assertFalse($comment->commenter->isVerified());

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
            'user_authentication' => 'guest_verified'
        ]);

        $article = Article::factory()->published()->create();

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

        $comment = Comment::withEmail($email)->first();

        $this->assertNotNull($comment);
        $this->assertNotNull($comment->commenter);
        $this->assertTrue($comment->commenter->isVerified());
    }

    /**
     * @test
     */
    #[Test]
    public function guest_can_post_comment_without_email_verification()
    {
        Mail::fake();
        PageSettings::fake('blog', [
            'user_authentication' => 'guest_unverified'
        ]);

        $article = Article::factory()->published()->create();

        [$name, $email] = [$this->faker->name, $this->faker->email];

        $response = $this->post(route('blog.comment', ['article' => $article]), [
            'comment' => $this->faker->realText(),
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertRedirect(); // Assuming redirect after submission

        $comment = Comment::withEmail($email)->first();
        $this->assertNotNull($comment);
        $this->assertEquals(Comment::STATUS_AWAITING_APPROVAL, $comment->status);
        $this->assertTrue($comment->commenter->isVerified());

        Mail::assertNotSent(function (Mailable $mail) use ($email) {
            return $mail->hasTo($email);
        });
    }
}
