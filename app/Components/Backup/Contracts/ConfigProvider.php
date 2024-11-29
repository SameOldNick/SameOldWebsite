<?php

namespace App\Components\Backup\Contracts;

use Spatie\Backup\Config\BackupConfig;
use Spatie\Backup\Config\CleanupConfig;
use Spatie\Backup\Config\MonitoredBackupsConfig;
use Spatie\Backup\Config\NotificationsConfig;

interface ConfigProvider
{
    /**
     * Gets the backup config
     */
    public function getBackup(): BackupConfig;

    /**
     * Gets notifications config
     */
    public function getNotifications(): NotificationsConfig;

    /**
     * Gets monitored backups config
     */
    public function getMonitoredBackups(): MonitoredBackupsConfig;

    /**
     * Gets cleanup config
     */
    public function getCleanup(): CleanupConfig;
}
