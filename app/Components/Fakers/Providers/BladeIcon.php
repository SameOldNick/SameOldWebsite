<?php

namespace App\Components\Fakers\Providers;

use BladeUI\Icons\Factory as BladeIconsFactory;
use Faker\Generator;
use Faker\Provider\Base;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Symfony\Component\Finder\SplFileInfo;

class BladeIcon extends Base
{
    public function __construct(
        Generator $generator,
        private readonly BladeIconsFactory $bladeIcons,
        private readonly FilesystemFactory $disks,
    ) {
        parent::__construct($generator);
    }

    /**
     * Generates a random Blade Icon name
     *
     * @param  string|null  $setKey  Set key (a random set is chosen if not specified)
     * @return string Icon name (with set prefix)
     */
    public function iconName($setKey = null): string
    {
        $set = $this->getSet($setKey ?? array_rand($this->getSets()));

        $file = $this->getRandomIconFile($set);

        return $this->getIconName($set, $file);
    }

    /**
     * Generates a random Blade Icon
     *
     * @param  string|null  $setKey  Set key (a random set is chosen if not specified)
     */
    public function icon($setKey = null): \BladeUI\Icons\Svg
    {
        $name = $this->iconName($setKey);

        return $this->bladeIcons->svg($name);
    }

    /**
     * Gets SVG markup for a random Blade Icon.
     *
     * @param  string|null  $setKey  Set key (a random set is chosen if not specified)
     * @return string
     */
    public function iconSvg($setKey = null)
    {
        return $this->icon($setKey)->contents();
    }

    /**
     * Gets HTML for a random Blade Icon.
     *
     * @param  string|null  $setKey  Set key (a random set is chosen if not specified)
     * @return string
     */
    public function iconHtml($setKey = null)
    {
        return $this->icon($setKey)->toHtml();
    }

    /**
     * Picks a path for an icon set.
     *
     * @param  array  $set  Icon set configuration
     */
    private function getRandomPath(array $set): string
    {
        return Arr::random($set['paths']);
    }

    /**
     * Gets a random icon file in set.
     *
     * @param  array  $set  Icon set configuration
     */
    private function getRandomIconFile(array $set): SplFileInfo
    {
        $files = $this->getFilesystem($set)->files($this->getRandomPath($set));

        return Arr::random($files);
    }

    /**
     * Gets the icon name (with prefix)
     *
     * @param  array  $set  Icon set configuration
     * @param  SplFileInfo  $file  Icon file
     * @return string Icon name
     */
    private function getIconName(array $set, SplFileInfo $file): string
    {
        $name = $file->getBasename('.'.$file->getExtension());

        return sprintf('%s-%s', $set['prefix'] ?? 'default', $name);
    }

    /**
     * Gets available Blade Icon sets.
     */
    private function getSets(): array
    {
        return $this->bladeIcons->all();
    }

    /**
     * Gets configuration for icon set.
     */
    private function getSet(string $key): array
    {
        $all = $this->getSets();

        if (! isset($all[$key])) {
            throw new InvalidArgumentException(sprintf("'%s' is not a valid icon set.", $key));
        }

        return $all[$key];
    }

    /**
     * Gets filesystem to access icon files.
     *
     * @param  array  $set  Icon set configuration
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    private function getFilesystem(array $set)
    {
        $default = config('blade-icons.sets');

        if (isset($set['disk']) || isset($default['disk'])) {
            return $this->disks->disk($set['disk'] ?? $default['disk']);
        } else {
            return app(FilesystemManager::class)->disk();
        }
    }
}
