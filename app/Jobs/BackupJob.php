<?php

namespace App\Jobs;

use App\Components\Websockets\Artisan;
use App\Components\Websockets\Notifiers\JobStatusNotifier;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ramsey\Uuid\UuidInterface;

class BackupJob extends NotifiableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        JobStatusNotifier $notifier,
        protected readonly array $params = []
    ) {
        parent::__construct($notifier);
    }

    /**
     * Handle the job.
     *
     * @return void
     */
    public function handler()
    {
        $artisan = Artisan::create($this->getNotifier()->getNotifiable(), $this->getNotifier()->getUuid());

        $errorCode = $artisan('backup:run', ['--no-interaction' => true] + $this->params);
    }
}
