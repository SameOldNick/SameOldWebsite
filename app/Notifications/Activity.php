<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\User;
use App\Enums\Notifications\ActivityEvent;
use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;

class Activity extends Notification
{
    use Queueable;

    const DATABASE_TYPE_UUID = 'ce659a33-08dd-4c9c-a421-7bb54393b76d';

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly ActivityEvent $event,
        public readonly DateTimeInterface $dateTime,
        public readonly string $message,
        public readonly array $context
    )
    {
        //
    }

    /**
     * Gets the type to store in the 'type' column in the database table.
     *
     * @return string
     */
    public function databaseType()
    {
        return static::DATABASE_TYPE_UUID;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'dateTime' => $this->dateTime,
            'event' => $this->event->value,
            'message' => $this->message,
            'context' => $this->context
        ];
    }
}
