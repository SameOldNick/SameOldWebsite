<?php

namespace App\Listeners\Contact;

use App\Events\Contact\ContactSubmissionApproved;
use App\Mail\Contacted;
use App\Models\Role;
use App\Notifications\MessageNotification;
use App\Traits\Support\NotifiesRoles;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class SendContactedMessages
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
     * Handle the event.
     */
    public function handle(ContactSubmissionApproved $event): void
    {
        $contacted = Contacted::create($event->name, $event->email, $event->message);

        Mail::send($contacted);

        $this->notifyRoles('admin', new MessageNotification($contacted));
    }
}
