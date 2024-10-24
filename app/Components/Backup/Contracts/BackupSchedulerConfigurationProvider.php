<?php

namespace App\Components\Backup\Contracts;

interface BackupSchedulerConfigurationProvider
{
    /**
     * Checks if backup should be scheduled
     *
     * @return boolean
     */
    public function isBackupEnabled(): bool;

    /**
     * Checks if backup cleaner should be scheduled
     *
     * @return boolean
     */
    public function isCleanupEnabled(): bool;

    /**
     * Gets the backup cron expression
     *
     * @return string
     */
    public function getBackupCronExpression(): string;

    /**
     * Gets the cleanup cron expression
     *
     * @return string
     */
    public function getCleanupCronExpression(): string;
}
