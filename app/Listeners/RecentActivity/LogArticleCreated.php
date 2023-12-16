<?php

namespace App\Listeners\RecentActivity;

use App\Events\Articles\ArticleCreated;
use App\Notifications\Activity;
use App\Enums\Notifications\ActivityEvent;

class LogArticleCreated extends LogActivity
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
    public function handle(ArticleCreated $event): void
    {
        $article = $event->article;
        $user = $event->user();

        $message = __('Article ":article" was created by ":user".', ['user' => $user->getDisplayName(), 'article' => $article->title]);
        $context = [
            'article' => $article,
            'user' => $user
        ];

        $this->log(new Activity(ActivityEvent::ArticleCreated, $article->post->created_at ?? now(), $message, $context));
    }
}
