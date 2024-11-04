<?php

namespace App\Components\Backup\Providers;

use App\Components\Backup\Contracts\BackupConfigurationProvider;

class BackupDatabaseConfigurationProvider extends DatabaseConfigurationProvider implements BackupConfigurationProvider
{
    /**
     * @inheritDoc
     */
    public function getDisks(): array
    {
        return $this->getArrayValue('backup_disks');
    }
}
