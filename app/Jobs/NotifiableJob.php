<?php

namespace App\Jobs;

use App\Components\Websockets\Notifiers\JobStatusNotifier;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Auth\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Throwable;

abstract class NotifiableJob
{
    protected User $user;

    protected UuidInterface $uuid;

    protected JobStatusNotifier $notifier;

    /**
     * Create a new job instance.
     */
    public function __construct(
        User $user,
        ?UuidInterface $uuid = null
    ) {
        $this->user = $user;
        $this->uuid = $uuid ?? $this->generateUuid();
        $this->notifier = new JobStatusNotifier($this->uuid, $this->user);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Application $app)
    {
        $this->notifier->start();

        if (method_exists($this, 'handler')) {
            $app->call([$this, 'handler']);
        }

        $this->notifier->completed();
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed(Throwable $exception)
    {
        $this->notifier->failed($exception);
    }

    public function generateUuid(): UuidInterface
    {
        return Uuid::getFactory()->uuid4();
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }
}
