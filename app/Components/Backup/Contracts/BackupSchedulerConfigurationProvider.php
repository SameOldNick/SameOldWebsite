<?php

namespace App\Components\Backup\Contracts;

interface BackupSchedulerConfigurationProvider
{
    /**
     * Checks if backup should be scheduled
     */
    public function isBackupEnabled(): bool;

    /**
     * Checks if backup cleaner should be scheduled
     */
    public function isCleanupEnabled(): bool;

    /**
     * Gets the backup cron expression
     */
    public function getBackupCronExpression(): string;

    /**
     * Gets the cleanup cron expression
     */
    public function getCleanupCronExpression(): string;
}
