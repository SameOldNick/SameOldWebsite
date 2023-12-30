<?php

namespace App\Providers;

use App\Events\Articles\ArticleCreated;
use App\Events\Articles\ArticleDeleted;
use App\Events\Articles\ArticlePublished;
use App\Events\Articles\ArticleScheduled;
use App\Events\Articles\ArticleUnpublished;
use App\Events\Comments\CommentApproved;
use App\Events\Comments\CommentCreated;
use App\Events\Contact\ContactSubmissionApproved;
use App\Events\Contact\ContactSubmissionRequiresApproval;
use App\Events\PageUpdated;
use App\Listeners\Contact\SendConfirmMessage;
use App\Listeners\Contact\SendContactedConfirmationMessage;
use App\Listeners\Contact\SendContactedMessages;
use App\Listeners\NotifyArticleAuthorCommentPosted;
use App\Listeners\NotifyCommentRepliedTo;
use App\Listeners\RecentActivity\LogArticleCreated;
use App\Listeners\RecentActivity\LogArticleDeleted;
use App\Listeners\RecentActivity\LogArticlePublished;
use App\Listeners\RecentActivity\LogArticleScheduled;
use App\Listeners\RecentActivity\LogArticleUnpublished;
use App\Listeners\RecentActivity\LogCommentCreated;
use App\Listeners\RecentActivity\LogUserRegistered;
use App\Listeners\RefreshUpdatedPages;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            LogUserRegistered::class,
        ],
        ContactSubmissionRequiresApproval::class => [
            SendConfirmMessage::class,
        ],
        ContactSubmissionApproved::class => [
            SendContactedMessages::class,
            SendContactedConfirmationMessage::class,
        ],
        ArticleCreated::class => [
            LogArticleCreated::class,
        ],
        ArticlePublished::class => [
            LogArticlePublished::class,
        ],
        ArticleScheduled::class => [
            LogArticleScheduled::class,
        ],
        ArticleUnpublished::class => [
            LogArticleUnpublished::class,
        ],
        ArticleDeleted::class => [
            LogArticleDeleted::class,
        ],
        CommentCreated::class => [
            LogCommentCreated::class,
        ],
        CommentApproved::class => [
            NotifyArticleAuthorCommentPosted::class,
            NotifyCommentRepliedTo::class,
        ],
        PageUpdated::class => [
            RefreshUpdatedPages::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
