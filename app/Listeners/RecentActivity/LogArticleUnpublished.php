<?php

namespace App\Listeners\RecentActivity;

use App\Enums\Notifications\ActivityEvent;
use App\Events\Articles\ArticleUnpublished;
use App\Notifications\Activity;

class LogArticleUnpublished extends LogActivity
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
    public function handle(ArticleUnpublished $event): void
    {
        $article = $event->article;
        $user = $event->user();

        $message = __('Article ":article" was unpublished by ":user".', ['article' => $article->title, 'user' => $user->getDisplayName()]);
        $context = [
            'article' => $article,
            'user' => $user,
        ];

        $this->log(new Activity(ActivityEvent::ArticleUnpublished, now(), $message, $context));
    }
}
