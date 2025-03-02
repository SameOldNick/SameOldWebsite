<?php

namespace App\Components\Moderator\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

trait CompilesList
{
    /**
     * Compiles list from configuration
     */
    protected function compileList(array $config): array
    {
        $compiled = [];

        foreach ($config as $entry) {
            $append = match ($entry['source']) {
                'config' => $this->getListFromConfig($entry['key']),
                'file' => $this->getListFromFile($entry),
                'require' => $this->getListFromRequire($entry['path'], $entry['key']),
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
     * @return array
     */
    protected function getListFromConfig(string $key)
    {
        return config($key, []);
    }

    /**
     * Gets list from file
     *
     * @return array
     */
    protected function getListFromFile(array $entry)
    {
        $contents = Storage::disk($entry['disk'])->get($entry['path']);

        return match ($entry['format']) {
            'json' => (array) json_decode($contents),
            default => explode("\n", $contents)
        };
    }

    /**
     * Gets list from require
     *
     * @return array
     */
    protected function getListFromRequire(string $path, string $key)
    {
        $array = require $path;

        return Arr::get($array, $key, []);
    }
}
