<?php

namespace App\Components\Moderator\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use function Safe\json_encode;

class UpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moderator:update
                            {--y|yes : Skip confirmation prompts}
                            {--no-disposable : Skip updating disposable emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates lists used for comment moderation.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->option('no-disposable')) {
            $this->updateDisposableEmails();
        }
    }

    /**
     * Update disposable email domains
     *
     * @return void
     */
    protected function updateDisposableEmails()
    {
        $url = 'https://raw.githubusercontent.com/disposable/disposable-email-domains/master/domains.txt';

        try {
            $this->info('Downloading list of disposable email domains...');

            $bar = $this->output->createProgressBar();

            $response = Http::withOptions([
                'progress' => function ($downloadTotal, $downloadedBytes) use ($bar) {
                    if ($bar->getMaxSteps() !== $downloadTotal) {
                        $bar->start($downloadTotal);
                    }

                    $bar->setProgress($downloadedBytes);
                },
                'on_stats' => function () use ($bar) {
                    $bar->finish();
                },
            ])->get($url)->throw();

            $lines = explode("\n", $response->body());

            if (empty($lines)) {
                $this->error('No domains were found.');
                exit(1);
            }

            if (! $this->option('yes') && $this->getStorage()->exists($this->getDisposableEmailsPath()) && ! $this->confirm('Are you sure you want to overwrite the disposable emails file?')) {
                return;
            }

            if (! $this->getStorage()->put('data/disposable-emails.json', json_encode($lines, JSON_PRETTY_PRINT))) {
                $this->error('An error occurred writing file.');
                exit(1);
            }

            $this->info('Updated disposable emails.');
        } catch (RequestException $ex) {
            $this->error('Unable to download disposable emails.');
            $this->error($ex->getMessage());
            exit(1);
        }
    }

    /**
     * Gets storage driver
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function getStorage()
    {
        return Storage::disk('local');
    }

    /**
     * Gets path to store disposable email domains
     *
     * @return string
     */
    protected function getDisposableEmailsPath(): string
    {
        return 'data/disposable-emails.json';
    }
}
