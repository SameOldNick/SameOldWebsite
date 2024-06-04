<?php

namespace App\Components\Websockets\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

abstract class BroadcastNotification extends Notification
{
    use Queueable;

    /**
     * The event name to be broadcasted.
     */
    protected string $broadcastAs;

    /**
     * The type of event being broadcasted.
     */
    protected string $broadcastType;

    /**
     * Get the notification's delivery channels.
     *
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast'];
    }

    /**
     * Gets the event name.
     * This is the 'event' field sent to the websocket.
     */
    public function broadcastAs(): string
    {
        return $this->broadcastAs ?: get_class($this);
    }

    /**
     * Get the type of the notification being broadcast.
     * This is included with the event data.
     *
     * @return string
     */
    public function broadcastType()
    {
        return $this->broadcastType ?: get_class($this);
    }

    /**
     * Get the channels the event should broadcast on.
     * If empty is returned, the event is broadcasted on the the 'App.Models.User.{id}' private channel.
     *
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        return [];
    }

    /**
     * Get the notification data to broadcast.
     *
     * @return array<string, mixed>
     */
    abstract public function toBroadcast(object $notifiable): array;
}
