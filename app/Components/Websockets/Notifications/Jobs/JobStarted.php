<?php

namespace App\Components\Websockets\Notifications\Jobs;

use App\Components\Websockets\Notifications\BroadcastNotification;
use DateTimeInterface;
use Illuminate\Broadcasting\PrivateChannel;

class JobStarted extends BroadcastNotification
{
    /**
     * When notification was created.
     */
    public readonly DateTimeInterface $dateTime;

    /**
     * {@inheritDoc}
     */
    protected string $broadcastAs = 'JobStarted';

    /**
     * {@inheritDoc}
     */
    protected string $broadcastType = 'a38f22bf-ad1e-426c-9be5-c98755c61bb5';

    /**
     * Creates a new JobStarted notification.
     *
     * @param  string  $jobId  Job UUID
     * @param  DateTimeInterface|null  $dateTime  When job failed. If null, the current date/time is used.
     */
    public function __construct(
        public readonly string $jobId,
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
