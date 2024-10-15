<?php

namespace App\Notifications;

use App\Components\Websockets\Notifications\BroadcastNotification;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Broadcasting\PrivateChannel;

class Alert extends BroadcastNotification
{
    /**
     * When notification was created.
     */
    public readonly DateTimeInterface $dateTime;

    /**
     * {@inheritDoc}
     */
    protected string $broadcastAs = 'Alert';

    /**
     * {@inheritDoc}
     */
    protected string $broadcastType = 'cffa9651-88f5-4247-abae-63df928e34b7';

    /**
     * Creates a new Alert notification.
     */
    public function __construct(
        public readonly User $user,
        public readonly string $color,
        public readonly string $message,
        public readonly ?string $link = null,
        ?DateTimeInterface $dateTime = null
    ) {
        $this->dateTime = $dateTime ?? now();
    }

    /**
     * Gets the type to store in the 'type' column in the database table.
     *
     * @return string
     */
    public function databaseType()
    {
        return $this->broadcastType();
    }

    /**
     * {@inheritDoc}
     */
    public function via(object $notifiable): array
    {
        return ['broadcast', 'database'];
    }

    /**
     * {@inheritDoc}
     */
    public function broadcastOn()
    {
        return new PrivateChannel("users.{$this->user->uuid}");
    }

    /**
     * {@inheritDoc}
     */
    public function toBroadcast(object $notifiable): array
    {
        return $this->toArray($notifiable);
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
            'id' => $this->id,
            'color' => $this->color,
            'message' => $this->message,
            'link' => $this->link,
        ];
    }
}
