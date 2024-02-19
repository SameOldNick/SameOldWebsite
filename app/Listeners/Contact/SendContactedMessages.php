<?php

namespace App\Listeners\Contact;

use App\Events\Contact\ContactSubmissionApproved;
use App\Mail\Contacted;
use App\Notifications\MessageNotification;
use App\Traits\Support\NotifiesRoles;
use Illuminate\Support\Facades\Mail;

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

        $this->notifyRoles('admin', new MessageNotification($contacted));
    }
}
