<?php

namespace App\Components\Backup\SpatieBackup;

use App\Components\Backup\Contracts\ConfigProvider;
use Spatie\Backup\Config\Config;
use Spatie\Backup\Config\BackupConfig;
use Spatie\Backup\Config\NotificationsConfig;
use Spatie\Backup\Config\MonitoredBackupsConfig;
use Spatie\Backup\Config\CleanupConfig;

class DatabaseConfigProvider extends Config implements ConfigProvider
{
    public function __construct(
        protected readonly Config $original
    ) {
        parent::__construct(
            $this->getBackup(),
            $this->getNotifications(),
            $this->getMonitoredBackups(),
            $this->getCleanup(),
        );
    }

    /**
     * @inheritDoc
     */
    public function getBackup(): BackupConfig
    {
        return new DatabaseBackupConfigProvider($this->original->backup);
    }

    /**
     * @inheritDoc
     */
    public function getNotifications(): NotificationsConfig
    {
        return $this->original->notifications;
    }

    /**
     * @inheritDoc
     */
    public function getMonitoredBackups(): MonitoredBackupsConfig
    {
        return $this->original->monitoredBackups;
    }

    /**
     * @inheritDoc
     */
    public function getCleanup(): CleanupConfig
    {
        return $this->original->cleanup;
    }
}
