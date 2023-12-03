<?php

namespace App\Providers;

use App\Events\Comments\CommentApproved;
use App\Events\Contact\ContactSubmissionRequiresApproval;
use App\Listeners\NotifyArticleAuthorCommentPosted;
use App\Listeners\NotifyCommentRepliedTo;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Listeners\Contact\SendConfirmMessage;
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
        ],
        ContactSubmissionRequiresApproval::class => [
            SendConfirmMessage::class,
        ],
        ],
        CommentApproved::class => [
            NotifyArticleAuthorCommentPosted::class,
            NotifyCommentRepliedTo::class,
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
