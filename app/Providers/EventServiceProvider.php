<?php

namespace App\Providers;

use App\Events\Articles\ArticleCreated;
use App\Events\Articles\ArticleDeleted;
use App\Events\Articles\ArticlePublished;
use App\Events\Articles\ArticleScheduled;
use App\Events\Articles\ArticleUnpublished;
use App\Events\Comments\CommentCreated;
use App\Events\Contact\ContactSubmissionConfirmed;
use App\Events\Contact\ContactSubmissionRequiresConfirmation;
use App\Events\PageUpdated;
use App\Listeners\Backup\FailedBackup;
use App\Listeners\Backup\SuccessfulBackup;
use App\Listeners\Backup\SuccessfulCleanup;
use App\Listeners\Comments\ModerateComment;
use App\Listeners\Comments\NotifyArticleAuthorCommentPosted;
use App\Listeners\Comments\NotifyCommentRepliedTo;
use App\Listeners\Contact\SendConfirmMessage;
use App\Listeners\Contact\SendContactedConfirmationMessage;
use App\Listeners\Contact\SendContactedMessages;
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
use Spatie\Backup\Events\BackupHasFailed;
use Spatie\Backup\Events\BackupWasSuccessful;
use Spatie\Backup\Events\CleanupWasSuccessful;

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
        ContactSubmissionRequiresConfirmation::class => [
            SendConfirmMessage::class,
        ],
        ContactSubmissionConfirmed::class => [
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
        PageUpdated::class => [
            RefreshUpdatedPages::class,
        ],
        BackupWasSuccessful::class => [
            SuccessfulBackup::class,
        ],
        BackupHasFailed::class => [
            FailedBackup::class,
        ],
        CleanupWasSuccessful::class => [
            SuccessfulCleanup::class,
        ],
    ];

    /**
     * The subscribers to register.
     *
     * @var array
     */
    protected $subscribe = [
        ModerateComment::class,
        NotifyArticleAuthorCommentPosted::class,
        NotifyCommentRepliedTo::class,
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
