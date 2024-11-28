<?php

namespace App\Components\Backup\Contracts;

interface FilesystemConfigurationFactory
{
    /**
     * Gets filesystem configuration
     *
     * @param string $name
     * @return FilesystemConfiguration|null
     */
    public function getFilesystemConfiguration(string $name): ?FilesystemConfiguration;
}
