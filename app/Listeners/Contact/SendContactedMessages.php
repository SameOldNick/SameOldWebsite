<?php

namespace App\Listeners\Contact;

use App\Events\Contact\ContactSubmissionConfirmed;
use App\Mail\Contacted;
use App\Notifications\MessageNotification;
use App\Traits\Support\NotifiesRoles;

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
    public function handle(ContactSubmissionConfirmed $event): void
    {
        $contacted = Contacted::create($event->message->name, $event->message->email, $event->message->message);

        $this->notifyRoles($this->getRoles(), new MessageNotification($contacted));
    }

    /**
     * Gets the roles to send message to.
     */
    protected function getRoles(): array
    {
        return ['receive_contact_messages'];
    }
}
