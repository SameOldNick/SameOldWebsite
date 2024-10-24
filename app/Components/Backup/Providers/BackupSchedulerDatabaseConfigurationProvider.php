<?php

namespace App\Components\Backup\Providers;

use App\Components\Backup\Contracts\BackupSchedulerConfigurationProvider;

class BackupSchedulerDatabaseConfigurationProvider extends DatabaseConfigurationProvider implements BackupSchedulerConfigurationProvider
{
    /**
     * @inheritDoc
     */
    public function isBackupEnabled(): bool
    {
        return $this->hasValue('backup_cron');
    }

    /**
     * @inheritDoc
     */
    public function isCleanupEnabled(): bool
    {
        return $this->hasValue('cleanup_cron');
    }

    /**
     * @inheritDoc
     */
    public function getBackupCronExpression(): string
    {
        return $this->getStringValue('backup_cron');
    }

    /**
     * @inheritDoc
     */
    public function getCleanupCronExpression(): string
    {
        return $this->getStringValue('cleanup_cron');
    }
}
