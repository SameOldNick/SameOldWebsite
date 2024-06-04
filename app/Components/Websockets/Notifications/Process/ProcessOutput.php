<?php

namespace App\Components\Websockets\Notifications\Process;

use App\Components\Websockets\Notifications\BroadcastNotification;
use DateTimeInterface;
use Illuminate\Broadcasting\PrivateChannel;

class ProcessOutput extends BroadcastNotification
{
    /**
     * {@inheritDoc}
     */
    protected string $broadcastAs = 'ProcessOutput';

    /**
     * {@inheritDoc}
     */
    protected string $broadcastType = '54de9906-539a-4984-adb8-53f109c78d2c';

    /**
     * When notification was created.
     */
    public readonly DateTimeInterface $dateTime;

    /**
     * Creates a new ProcessOutput notification.
     *
     * @param  string  $processId  Process UUID
     * @param  DateTimeInterface|null  $dateTime  When process outputted. If null, the current date/time is used.
     */
    public function __construct(
        public readonly string $processId,
        public readonly string $message,
        public readonly bool $newline,
        ?DateTimeInterface $dateTime = null
    ) {
        $this->dateTime = $dateTime ?? now();
    }

    /**
     * {@inheritDoc}
     */
    public function broadcastOn()
    {
        return new PrivateChannel("processes.{$this->processId}");
    }

    /**
     * {@inheritDoc}
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'dateTime' => $this->dateTime,
            'message' => $this->message,
            'newline' => $this->newline,
        ];
    }
}
