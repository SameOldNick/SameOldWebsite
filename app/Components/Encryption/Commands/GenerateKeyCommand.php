<?php

namespace App\Components\Encryption\Commands;

use Exception;
use Illuminate\Console\Command;
use function Safe\file_put_contents;

class GenerateKeyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encryption:generate {path? : File to write private key to} {--driver= : Encryption driver to use} {--force : If true, does not prompt user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a private key for signing.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $driver = $this->getLaravel()->make('encryption.signer')->driver($this->option('driver'));

            $contents = $driver->generate();

            $path = $this->argument('path');

            if (! is_null($path)) {
                $fullPath = base_path($path);

                if (file_exists($fullPath)) {
                    if (! is_writable($fullPath)) {
                        $this->error(sprintf('File "%s" is not writable.', $fullPath));

                        return static::FAILURE;
                    }

                    if (! $this->option('force') && ! $this->confirm(sprintf('File "%s" already exists. Would you like to overwrite it?', $fullPath))) {
                        return static::FAILURE;
                    }
                }

                file_put_contents($fullPath, $contents);

                $this->info(sprintf('Successfully wrote key to "%s".', $fullPath));
            } else {
                $this->line($contents);
            }
        } catch (Exception $ex) {
            dd($ex);
            $this->error($ex->getMessage());

            return static::FAILURE;
        }
    }
}
