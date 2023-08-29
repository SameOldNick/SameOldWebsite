<?php

namespace App\Console\Commands;

use BladeUI\Icons\Factory;
use BladeUI\Icons\IconsManifest;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SimpleXMLElement;

class BladeIconsJsonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'icons:json {--force : If set, overwrites file if it exists.} {file : Where to write JSON file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates JSON file of icons for use in JavaScript.';

    /**
     * Execute the console command.
     */
    public function handle(Factory $factory, IconsManifest $iconsManifest, Filesystem $filesystem)
    {
        $file = $this->argument('file');

        $sets = $factory->all();
        $manifest = $iconsManifest->getManifest($sets);

        $icons = [];

        foreach ($manifest as $set => $resources) {
            $prefix = $sets[$set]['prefix'];

            $icons[$set] = [
                'prefix' => $prefix,
                'icons' => [],
            ];

            foreach (collect($resources)->flatten() as $name) {
                $svg = svg(sprintf('%s-%s', $prefix, $name));

                $xml = simplexml_load_string($svg->contents());

                $icons[$set]['icons'][$name] = $this->xmlToArray($xml);
            }
        }

        if ($filesystem->exists($file)) {
            $this->info(sprintf('File "%s" already exists.', $file));

            if (! $this->option('force') && ! $this->confirm('Do you want to overwrite it?')) {
                return 0;
            }

            $this->info(sprintf('File "%s" will be overwritten.', $file));
        }

        $encoded = json_encode($icons, JSON_PRETTY_PRINT);

        if (! $filesystem->put($file, $encoded)) {
            $this->error(sprintf('Unable to write to file "%s".', $file));

            return 1;
        }

        $this->info(sprintf('Successfully wrote to file "%s".', $file));

        return 0;
    }

    private function xmlToArray(SimpleXMLElement $xml)
    {
        $props = [];
        $children = [];

        foreach ($xml->attributes() as $key => $value) {
            $props[$key] = (string) $value;
        }

        foreach ($xml->children() as $child) {
            array_push($children, $this->xmlToArray($child));
        }

        $parsed = [
            'tag' => $xml->getName(),
            'props' => $props,
        ];

        if (! empty($children)) {
            $parsed['children'] = $children;
        }

        return $parsed;
    }
}
