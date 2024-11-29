<?php

namespace App\Components\Backup\Concerns;

use App\Components\Backup\Contracts\BackupConfigurationProvider;

trait UsesBackupConfigurationProvider
{
    /**
     * Gets the backup configuration provider
     */
    protected function getConfigurationProvider(): BackupConfigurationProvider
    {
        return app(BackupConfigurationProvider::class);
    }
}
