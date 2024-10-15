<?php

namespace App\Listeners;

use App\Events\Contact\ContactSubmissionConfirmed;
use App\Notifications\Alert;
use App\Traits\Support\NotifiesRoles;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Notification;

class NotificationListeners
{
    use NotifiesRoles;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handles ContactSubmissionConfirmed event
     */
    public function handleContactSubmissionConfirmed(ContactSubmissionConfirmed $event): void
    {
        $message = $event->message;

        $this->notifyRoles(['receive_contact_messages'], Alert::create(
            'info',
            "A contact message was sent by '{$message->email}'.",
            '/admin/contact/messages'
        ));
    }

    /**
     * Handles Login event
     */
    public function handleAuthLogin(Login $event): void
    {
        $ipAddress = request()->ip();

        $message = $ipAddress ?
            sprintf("Somebody with IP address '%s' logged in to your account.", $ipAddress) :
            'Somebody logged in to your account.';

        Notification::send($event->user, Alert::create('info', $message));
    }

    /**
     * Handles Failed event
     */
    public function handleAuthFailed(Failed $event): void
    {
        $ipAddress = request()->ip();

        $message = $ipAddress ?
            sprintf("Somebody with IP address '%s' tried to login to your account.", $ipAddress) :
            'Somebody tried to login to your account.';

        Notification::send($event->user, Alert::create('info', $message));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            ContactSubmissionConfirmed::class => 'handleContactSubmissionConfirmed',
            Login::class => 'handleAuthLogin',
            Failed::class => 'handleAuthFailed',
        ];
    }
}
