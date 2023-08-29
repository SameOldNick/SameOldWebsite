<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class EmojisGenerateRegex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emojis:regex {--download : If set, the sequences file is downloaded from specified URL.} {path : File or URL of emoji sequence list}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates regex for detecting emojis';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('download')) {
            // https://www.unicode.org/Public/UCD/latest/ucd/emoji/emoji-data.txt
            $response = Http::get($this->argument('path'))->throw();

            $contents = $response->body();
        } else {
            $path = $this->argument('path');

            if (! is_readable($path)) {
                $this->error(sprintf('Unable to read file: %s', $path));

                return Command::FAILURE;
            }

            $contents = file_get_contents($path);
        }

        $lines = Str::of($contents)->split('/[\r|\n]/')->map('trim');

        $codes = collect();

        foreach ($lines as $line) {
            if (empty($line) || Str::startsWith($line, '#')) {
                continue;
            }

            foreach (['#', ';'] as $delimiter) {
                if (Str::contains($line, $delimiter)) {
                    $line = Str::before($line, $delimiter);
                }
            }

            $parts = explode('..', trim($line), 2);

            $codes->push(count($parts) === 2 ? [$this->getAsciiCode($parts[0]), $this->getAsciiCode($parts[1])] : [$this->getAsciiCode($parts[0])]);
        }

        return Command::SUCCESS;
    }

    protected function getAsciiCode(string $codepoint)
    {
        return hexdec($codepoint);
    }
}
