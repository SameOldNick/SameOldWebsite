<?php

namespace App\Components\Websockets\Notifications\Process;

use App\Components\Websockets\Notifications\BroadcastNotification;
use DateTimeInterface;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcessBegin extends BroadcastNotification
{
    /**
     * @inheritDoc
     */
    protected string $broadcastAs = 'ProcessBegin';

    /**
     * @inheritDoc
     */
    protected string $broadcastType = '9f663dfc-b4fc-4466-bbba-f032b351f2b5';

    /**
     * When notification was created.
     *
     * @var DateTimeInterface
     */
    public readonly DateTimeInterface $dateTime;

    /**
     * Creates a new ProcessBegin notification.
     *
     * @param string $processId Process UUID
     * @param DateTimeInterface|null $dateTime When process began. If null, the current date/time is used.
     */
    public function __construct(
        public readonly string $processId,
        ?DateTimeInterface $dateTime = null
    )
    {
        $this->dateTime = $dateTime ?? now();
    }

    /**
     * @inheritDoc
     */
    public function broadcastOn()
    {
        return new PrivateChannel("processes.{$this->processId}");
    }

    /**
     * @inheritDoc
     */
    public function toBroadcast(object $notifiable): array {
        return [
            'dateTime' => $this->dateTime
        ];
    }
}
