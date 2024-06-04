<?php

namespace App\Components\Websockets\Notifications\Jobs;

use App\Components\Websockets\Notifications\BroadcastNotification;
use DateTimeInterface;
use Illuminate\Broadcasting\PrivateChannel;

class JobFailed extends BroadcastNotification
{
    /**
     * {@inheritDoc}
     */
    protected string $broadcastAs = 'JobFailed';

    /**
     * {@inheritDoc}
     */
    protected string $broadcastType = '905488b7-327e-4cce-bca2-67b148f496f0';

    /**
     * When notification was created.
     */
    public readonly DateTimeInterface $dateTime;

    /**
     * Creates a JobFailed notification instance.
     *
     * @param  string  $jobId  Job UUID
     * @param  string  $message  Message from exception that caused job to fail
     * @param  DateTimeInterface|null  $dateTime  When job failed. If null, the current date/time is used.
     */
    public function __construct(
        public readonly string $jobId,
        public readonly string $message,
        ?DateTimeInterface $dateTime = null
    ) {
        $this->dateTime = $dateTime ?? now();
    }

    /**
     * {@inheritDoc}
     */
    public function broadcastOn()
    {
        return new PrivateChannel("jobs.{$this->jobId}");
    }

    /**
     * {@inheritDoc}
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'dateTime' => $this->dateTime,
        ];
    }
}
