<?php

namespace App\Listeners\RecentActivity;

use App\Events\Articles\ArticleDeleted;
use App\Notifications\Activity;
use App\Enums\Notifications\ActivityEvent;

class LogArticleDeleted extends LogActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ArticleDeleted $event): void
    {
        $article = $event->article;
        $user = $event->user();

        $message = __('Article ":article" was deleted by ":user".', ['article' => $article->title, 'user' => $user->getDisplayName()]);
        $context = [
            'article' => $article,
            'user' => $user
        ];

        $this->log(new Activity(ActivityEvent::ArticleDeleted, $article->post->deleted_at ?? now(), $message, $context));
    }
}
