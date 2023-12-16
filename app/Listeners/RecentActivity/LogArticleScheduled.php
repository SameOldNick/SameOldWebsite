<?php

namespace App\Listeners\RecentActivity;

use App\Events\Articles\ArticleCreated;
use App\Events\Articles\ArticlePublished;
use App\Events\Articles\ArticleScheduled;
use App\Notifications\Activity;
use App\Enums\Notifications\ActivityEvent;

class LogArticleScheduled extends LogActivity
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
    public function handle(ArticleScheduled $event): void
    {
        $article = $event->article;
        $user = $event->user();

        $message = __('Article ":article" was scheduled for :dateTime by ":user".', [
            'article' => $article->title,
            'dateTime' => $article->published_at->toDateTimeString(),
            'user' => $user->getDisplayName()
        ]);
        $context = [
            'article' => $article,
            'user' => $user
        ];

        $this->log(new Activity(ActivityEvent::ArticleScheduled, now(), $message, $context));
    }
}
