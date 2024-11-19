<?php

namespace App\Components\Backup\Contracts;

use Spatie\Backup\Config\BackupConfig;
use Spatie\Backup\Config\NotificationsConfig;
use Spatie\Backup\Config\MonitoredBackupsConfig;
use Spatie\Backup\Config\CleanupConfig;

interface ConfigProvider
{
    /**
     * Gets the backup config
     *
     * @return BackupConfig
     */
    public function getBackup(): BackupConfig;

    /**
     * Gets notifications config
     *
     * @return NotificationsConfig
     */
    public function getNotifications(): NotificationsConfig;
    
    /**
     * Gets monitored backups config
     *
     * @return MonitoredBackupsConfig
     */
    public function getMonitoredBackups(): MonitoredBackupsConfig;

    /**
     * Gets cleanup config
     *
     * @return CleanupConfig
     */
    public function getCleanup(): CleanupConfig;
}
