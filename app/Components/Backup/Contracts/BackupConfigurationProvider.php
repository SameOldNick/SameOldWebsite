<?php

namespace App\Components\Backup\Contracts;

interface BackupConfigurationProvider
{
    /**
     * Gets disks to use for backups
     */
    public function getDisks(): array;
}
