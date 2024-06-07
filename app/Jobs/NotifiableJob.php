<?php

namespace App\Jobs;

use App\Components\Websockets\Notifiers\JobStatusNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

abstract class NotifiableJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * In order for the job to be serialized for later processing by the queue:
     *  - The properties cannot be set as readonly.
     *  - The properties must have a visibility of at least protected (not private).
     */

    /**
     * Job status notifier
     *
     * @var JobStatusNotifier
     */
    protected JobStatusNotifier $notifier;

    /**
     * Create a new job instance.
     */
    public function __construct(JobStatusNotifier $notifier) {
        $this->notifier = $notifier;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Application $app)
    {
        $this->getNotifier()->start();

        if (method_exists($this, 'handler')) {
            $app->call([$this, 'handler']);
        }

        $this->getNotifier()->completed();
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed(Throwable $exception)
    {
        $this->getNotifier()->failed($exception);

    }

    /**
     * Gets the job status notifier
     *
     * @return JobStatusNotifier
     */
    public function getNotifier(): JobStatusNotifier {
        return $this->notifier;
    }
}
