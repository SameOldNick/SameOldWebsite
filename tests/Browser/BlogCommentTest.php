<?php

namespace Tests\Browser;

use App\Models\Article;
use App\Components\Settings\Facades\PageSettings;
use App\Models\Page;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Browser\Pages\Blog\Article as BlogArticle;

class BlogCommentTest extends DuskTestCase
{
    use DatabaseTruncation;
    use WithFaker;

    /**
     * Tests the comment form is displayed for a guest.
     */
    #[Test]
    public function displays_comment_form_guest(): void
    {
        Page::firstWhere(['page' => 'blog'])->metaData()->createMany([
            ['key' => 'user_authentication', 'value' => 'guest_verified'],
            ['key' => 'comment_moderation', 'value' => 'manual'],
            ['key' => 'use_captcha', 'value' => 'guest'],
        ]);

        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->published()->create();

        $this->browse(function (Browser $browser) use ($article) {
            $browser->visit(new BlogArticle($article))
                    ->assertGuest()
                    ->click('input#uncollapseLeaveComment')
                    ->waitFor('@commentForm')
                    ->assertVisible('@commentForm');
        });
    }

    /**
     * Tests the comment form is not displayed for a guest.
     */
    #[Test]
    public function doesnt_display_comment_form_guest(): void
    {
        Page::firstWhere(['page' => 'blog'])->metaData()->createMany([
            ['key' => 'user_authentication', 'value' => 'registered'],
            ['key' => 'comment_moderation', 'value' => 'manual'],
            ['key' => 'use_captcha', 'value' => 'guest'],
        ]);

        $article = Article::factory()->withRevision()->createPostWithRegisteredPerson()->published()->create();

        $this->browse(function (Browser $browser) use ($article) {
            $browser->visit(new BlogArticle($article))
                    ->assertGuest()
                    ->assertNotPresent('@commentForm');
        });
    }
}
