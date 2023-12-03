<?php

namespace App\Listeners\Contact;

use App\Events\Contact\ContactSubmissionApproved;
use App\Mail\Contacted;
use App\Models\Role;
use App\Notifications\MessageNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class SendContactedMessages
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
    public function handle(ContactSubmissionApproved $event): void
    {
        $contacted = Contacted::create($event->name, $event->email, $event->message);

        Mail::send($contacted);

        $admins = Role::firstWhere(['role' => 'admin'])->users;
        Notification::send($admins, new MessageNotification($contacted));
    }
}
