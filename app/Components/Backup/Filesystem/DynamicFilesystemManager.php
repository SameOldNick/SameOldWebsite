<?php

namespace App\Components\Backup\Filesystem;

use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Str;
use App\Components\Backup\Contracts\FilesystemConfiguration as FilesystemConfigurationContract;
use App\Components\Backup\Contracts\FilesystemConfigurationFactory;

class DynamicFilesystemManager extends FilesystemManager
{
    public readonly FilesystemConfigurationFactory $configurationFactory;

    public function __construct($app, FilesystemConfigurationFactory $configurationFactory)
    {
        parent::__construct($app);

        $this->configurationFactory = $configurationFactory;
    }

    /**
     * @inheritDoc
     */
    protected function resolve($name, $config = null)
    {
        if (Str::startsWith($name, 'dynamic-')) {
            $config = $this->getDynamicFilesystemConfiguration($name);

            if ($config) {
                return $this->createCustomDriver($this->getDriverConfig($config));
            }
        }

        // Fallback to parent if no dynamic config is found
        return parent::resolve($name);
    }

    /**
     * Gets filesystem configuration for dynamic disk.
     *
     * @param string $name Disk name (prefixed with dynamic-)
     * @return FilesystemConfigurationContract|null
     */
    protected function getDynamicFilesystemConfiguration(string $name): ?FilesystemConfigurationContract
    {
        return $this->configurationFactory->getFilesystemConfiguration($name);
    }

    /**
     * Creates driver from config
     *
     * @param array $config
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function createCustomDriver(array $config)
    {
        // $config must have 'driver' key set
        // If not, the parent resolve method will thrown an exception

        return parent::resolve($config['name'], $config);
    }

    /**
     * Gets filesystem config from FilesystemConfigurationContract
     *
     * @param FilesystemConfigurationContract $config
     * @return array
     */
    protected function getDriverConfig(FilesystemConfigurationContract $config): array
    {
        return $config->getFilesystemConfig();
    }
}
