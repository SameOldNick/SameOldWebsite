<?php

namespace App\Components\Backup\Contracts;

interface FilesystemConfigurationFactory
{
    /**
     * Gets filesystem configuration
     */
    public function getFilesystemConfiguration(string $name): ?FilesystemConfiguration;
}
