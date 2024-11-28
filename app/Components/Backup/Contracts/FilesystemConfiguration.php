<?php

namespace App\Components\Backup\Contracts;

interface FilesystemConfiguration
{
    /**
     * Gets the filesystem configuration.
     * The array will have the same structure as the drivers/disks in config\filesystems.php
     *
     * @return array
     */
    public function getFilesystemConfig(): array;
}
