<?php

namespace App\Components\Websockets\Notifications\Jobs;

use App\Components\Websockets\Notifications\BroadcastNotification;
use DateTimeInterface;
use Illuminate\Broadcasting\PrivateChannel;

class JobCompleted extends BroadcastNotification
{
    /**
     * When notification was created.
     */
    public readonly DateTimeInterface $dateTime;

    /**
     * {@inheritDoc}
     */
    protected string $broadcastAs = 'JobCompleted';

    /**
     * {@inheritDoc}
     */
    protected string $broadcastType = '5e390910-fd3f-40b8-aa2f-3baebac890fe';

    /**
     * Creates new JobCompleted notification.
     *
     * @param  string  $jobId  Job UUID
     * @param  DateTimeInterface|null  $dateTime  When the job completed. If null, the current date/time is used.
     */
    public function __construct(
        public readonly string $jobId,
        ?DateTimeInterface $dateTime = null)
    {
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
