<?php

namespace App\Components\Websockets\Notifications\Process;

use App\Components\Websockets\Notifications\BroadcastNotification;
use DateTimeInterface;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcessComplete extends BroadcastNotification
{
    /**
     * @inheritDoc
     */
    protected string $broadcastAs = 'ProcessComplete';

    /**
     * @inheritDoc
     */
    protected string $broadcastType = '14875796-d41b-4497-a673-e2a91c3254e0';

    /**
     * When notification was created.
     *
     * @var DateTimeInterface
     */
    public readonly DateTimeInterface $dateTime;

    /**
     * Creates a new ProcessComplete notification.
     *
     * @param string $processId Process UUID
     * @param DateTimeInterface|null $dateTime When process completed. If null, the current date/time is used.
     */
    public function __construct(
        public readonly string $processId,
        public readonly int $errorCode,
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
            'dateTime' => $this->dateTime,
            'errorCode' => $this->errorCode
        ];
    }
}
