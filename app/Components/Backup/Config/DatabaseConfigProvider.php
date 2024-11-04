<?php

namespace App\Components\Backup\Config;

use App\Components\Backup\Contracts\ConfigProvider;
use Illuminate\Contracts\Container\Container;
use Spatie\Backup\Config\Config;
use Spatie\Backup\Config\BackupConfig;
use Spatie\Backup\Config\NotificationsConfig;
use Spatie\Backup\Config\MonitoredBackupsConfig;
use Spatie\Backup\Config\CleanupConfig;

class DatabaseConfigProvider extends Config implements ConfigProvider
{
    public function __construct(
        protected readonly Container $app,
        protected readonly Config $config
    ) {
        $this->backup = $this->getBackup();
        $this->notifications = $this->getNotifications();
        $this->monitoredBackups = $this->getMonitoredBackups();
        $this->cleanup = $this->getCleanup();
    }

    /**
     * Gets the backup config
     *
     * @return BackupConfig
     */
    public function getBackup(): BackupConfig
    {
        return new DatabaseBackupConfigProvider($this->config->backup);
    }

    /**
     * Gets notifications config
     *
     * @return NotificationsConfig
     */
    public function getNotifications(): NotificationsConfig
    {
        return $this->config->notifications;
    }

    /**
     * Gets monitored backups config
     *
     * @return MonitoredBackupsConfig
     */
    public function getMonitoredBackups(): MonitoredBackupsConfig
    {
        return $this->config->monitoredBackups;
    }

    /**
     * Gets cleanup config
     *
     * @return CleanupConfig
     */
    public function getCleanup(): CleanupConfig
    {
        return $this->config->cleanup;
    }
}
