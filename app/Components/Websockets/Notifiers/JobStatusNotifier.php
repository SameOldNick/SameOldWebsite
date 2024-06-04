<?php

namespace App\Components\Websockets\Notifiers;

use App\Components\Websockets\Notifications\Jobs\JobStarted;
use App\Components\Websockets\Notifications\Jobs\JobFailed;
use App\Components\Websockets\Notifications\Jobs\JobCompleted;
use Ramsey\Uuid\UuidInterface;
use DateTimeInterface;
use Illuminate\Foundation\Auth\User;
use Throwable;

class JobStatusNotifier extends AbstractNotifier
{
    /**
     * Initializes JobStatusNotifier instance
     *
     * @param UuidInterface $uuid Job UUID
     * @param object $notifiable Who to route notifications to
     */
    public function __construct(
        public readonly UuidInterface $uuid,
        public readonly object $notifiable,
    )
    {
    }

    /**
     * Sends notification that job was started.
     *
     * @param DateTimeInterface|null $dateTime
     * @return void
     */
    public function start(?DateTimeInterface $dateTime = null)
    {
        $this->notify($this->notifiable, new JobStarted($this->uuid, $dateTime));
    }

    /**
     * Sends notification that job failed.
     *
     * @param Throwable $exception
     * @param DateTimeInterface|null $dateTime
     * @return void
     */
    public function failed(Throwable $exception, ?DateTimeInterface $dateTime = null)
    {
        // The exception message can only be used because serializing \Exception causes error "Serialization of 'Closure' is not allowed"
        $this->notify($this->notifiable, new JobFailed($this->uuid, $exception->getMessage(), $dateTime));
    }

    /**
     * Sends notification that job completed.
     *
     * @param DateTimeInterface|null $dateTime
     * @return void
     */
    public function completed(?DateTimeInterface $dateTime = null)
    {
        $this->notify($this->notifiable, new JobCompleted($this->uuid, $dateTime));
    }
}
