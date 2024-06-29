<?php

namespace App\Components\Moderator\Concerns;

use Illuminate\Support\Facades\Storage;

trait CompilesList {
    /**
     * Compiles list from configuration
     *
     * @param array $config
     * @return array
     */
    protected function compileList(array $config): array {
        $compiled = [];

        foreach ($config as $entry) {
            $append = match ($entry['source']) {
                'config' => $this->getListFromConfig($entry['key']),
                'file' => $this->getListFromFile($entry),
                'inline' => $entry['list'],
                default => []
            };

            array_push($compiled, ...$append);
        }

        return $compiled;
    }

    /**
     * Gets list built-in to config
     *
     * @param string $key
     * @return array
     */
    protected function getListFromConfig(string $key) {
        return config($key, []);
    }

    /**
     * Gets list from file
     *
     * @param array $entry
     * @return array
     */
    protected function getListFromFile(array $entry) {
        $contents = Storage::disk($entry['disk'])->get($entry['path']);

        return match ($entry['format']) {
            'json' => (array) json_decode($contents),
            default => explode("\n", $contents)
        };
    }

}
