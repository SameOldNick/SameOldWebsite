<?php

namespace App\Components\Websockets\Notifiers;

use App\Components\Websockets\Notifications\Jobs\JobCompleted;
use App\Components\Websockets\Notifications\Jobs\JobFailed;
use App\Components\Websockets\Notifications\Jobs\JobStarted;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;
use Throwable;

class JobStatusNotifier extends AbstractNotifier
{
    /**
     * Initializes JobStatusNotifier instance
     *
     * @param  UuidInterface  $uuid  Job UUID
     * @param  object  $notifiable  Who to route notifications to
     */
    public function __construct(
        public readonly UuidInterface $uuid,
        public readonly object $notifiable,
    ) {
    }

    /**
     * Sends notification that job was started.
     *
     * @return void
     */
    public function start(?DateTimeInterface $dateTime = null)
    {
        $this->notify($this->notifiable, new JobStarted($this->uuid, $dateTime));
    }

    /**
     * Sends notification that job failed.
     *
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
     * @return void
     */
    public function completed(?DateTimeInterface $dateTime = null)
    {
        $this->notify($this->notifiable, new JobCompleted($this->uuid, $dateTime));
    }
}
