<?php

namespace App\Components\Websockets\Notifiers;

use App\Components\Websockets\Notifications\Jobs\JobCompleted;
use App\Components\Websockets\Notifications\Jobs\JobFailed;
use App\Components\Websockets\Notifications\Jobs\JobStarted;
use App\Models\PrivateChannel;
use DateTimeInterface;
use Illuminate\Support\Str;
use Ramsey\Uuid\UuidInterface;
use Throwable;

class JobStatusNotifier extends AbstractNotifier
{
    /**
     * Job UUID
     */
    private UuidInterface $uuid;

    /**
     * Who to notify
     */
    private object $notifiable;

    /**
     * Channel to broadcast notifications to
     */
    protected ?PrivateChannel $channel;

    /**
     * Initializes JobStatusNotifier instance
     *
     * @param  UuidInterface  $uuid  Job UUID
     * @param  object  $notifiable  Who to route notifications to
     */
    public function __construct(
        UuidInterface $uuid,
        object $notifiable,
    ) {
        $this->uuid = $uuid;
        $this->notifiable = $notifiable;
    }

    /**
     * Opens channel.
     *
     * @return $this
     */
    public function openChannel(?DateTimeInterface $expiresAt = null): static
    {
        $this->channel = PrivateChannel::open($this->uuid, $this->notifiable, $expiresAt);

        return $this;
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

    /**
     * Closes channel.
     *
     * @return $this
     */
    public function closeChannel(): static
    {
        if (! is_null($this->channel)) {
            $this->channel->close();

            $this->channel = null;
        }

        return $this;
    }

    /**
     * Gets the UUID
     */
    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    /**
     * Gets who to notify
     */
    public function getNotifiable(): object
    {
        return $this->notifiable;
    }

    /**
     * Gets private channel
     */
    public function getChannel(): ?PrivateChannel
    {
        return $this->channel;
    }

    /**
     * Creates JobStatusNotifier instance
     */
    public static function create(object $notifiable, ?UuidInterface $uuid = null): JobStatusNotifier
    {
        return new JobStatusNotifier($uuid ?? static::generateUuid(), $notifiable);
    }

    /**
     * Generates UUID
     */
    public static function generateUuid(): UuidInterface
    {
        return Str::uuid();
    }
}
