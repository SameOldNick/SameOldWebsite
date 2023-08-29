<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class MessageNotification extends Notification
{
    use Queueable;

    private $factory;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private Mailable $mailable
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): Mailable
    {
        return $this->mailable;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $data = method_exists($this->mailable, 'toArray') ? $this->mailable->toArray() : $this->getDefaultData();

        return [
            ...$data,
            'type' => get_class($this->mailable),
        ];
    }

    protected function getDefaultData()
    {
        $addresses = [];

        foreach (['to', 'cc', 'bcc', 'replyTo'] as $type) {
            if (! empty($this->mailable->{$type})) {
                $addresses[$type] = $this->mailable->{$type};
            }
        }

        return [
            'addresses' => $addresses,
            'subject' => $this->mailable->subject,
            'message' => $this->mailable->render(),
        ];
    }
}
