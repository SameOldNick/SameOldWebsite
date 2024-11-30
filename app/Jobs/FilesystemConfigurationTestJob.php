<?php

namespace App\Jobs;

use App\Components\Websockets\Artisan;
use App\Components\Websockets\Notifiers\JobStatusNotifier;
use App\Models\FilesystemConfiguration;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class FilesystemConfigurationTestJob extends NotifiableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        JobStatusNotifier $notifier,
        protected readonly FilesystemConfiguration $filesystemConfiguration,
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
        try {
            // Simply try to get files.
            Storage::disk($this->filesystemConfiguration->driver_name)->files();
        } catch (Exception $ex) {
            // Manually fail the job so it's not attempted again.
            $this->fail($ex);
        }
    }
}