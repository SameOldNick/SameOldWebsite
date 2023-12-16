<?php

namespace App\Listeners\RecentActivity;

use App\Events\Articles\ArticleCreated;
use App\Events\Articles\ArticlePublished;
use App\Notifications\Activity;
use App\Enums\Notifications\ActivityEvent;

class LogArticlePublished extends LogActivity
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
    public function handle(ArticlePublished $event): void
    {
        $article = $event->article;
        $user = $event->user();

        $message = __('Article ":article" was published by ":user".', ['article' => $article->title, 'user' => $user->getDisplayName()]);
        $context = [
            'article' => $article,
            'user' => $user
        ];

        $this->log(new Activity(ActivityEvent::ArticlePublished, $article->published_at ?? now(), $message, $context));
    }
}
