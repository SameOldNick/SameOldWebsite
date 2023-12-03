<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class MessageNotification extends Notification
{
    use Queueable;

    const DATABASE_TYPE_UUID = '6414fd8c-847a-492b-a919-a5fc539456e8';

    private $factory;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private Mailable $mailable
    ) {
    }

    /**
     * Gets the type to store in the 'type' column in the database table.
     *
     * @return string
     */
    public function databaseType() {
        return static::DATABASE_TYPE_UUID;
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
