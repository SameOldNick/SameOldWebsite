<?php

namespace App\Listeners\Contact;

use App\Events\Contact\ContactSubmissionApproved;
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
    public function handle(ContactSubmissionApproved $event): void
    {
        $contacted = Contacted::create($event->name, $event->email, $event->message);

        $this->notifyRoles($this->getRoles(), new MessageNotification($contacted));
    }

    /**
     * Gets the roles to send message to.
     *
     * @return array
     */
    protected function getRoles(): array {
        return ['receive_contact_messages'];
    }
}
