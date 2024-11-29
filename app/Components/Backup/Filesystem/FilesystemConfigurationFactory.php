<?php

namespace App\Components\Backup\Filesystem;

use App\Components\Backup\Contracts\FilesystemConfigurationFactory as FactoryContract;
use App\Models\FilesystemConfiguration;
use Illuminate\Support\Str;

class FilesystemConfigurationFactory implements FactoryContract
{
    /**
     * {@inheritDoc}
     */
    public function getFilesystemConfiguration(string $name): ?FilesystemConfiguration
    {
        return FilesystemConfiguration::where('name', Str::substr($name, 8))->first();
    }
}
