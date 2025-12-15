<?php

namespace App\Components\Backup\SpatieBackup;

use App\Components\Backup\Contracts\ConfigProvider;
use App\Components\Backup\Contracts\NotificationConfigurationProviderInterface;
use Spatie\Backup\Config\BackupConfig;
use Spatie\Backup\Config\CleanupConfig;
use Spatie\Backup\Config\Config;
use Spatie\Backup\Config\MonitoredBackupsConfig;
use Spatie\Backup\Config\NotificationsConfig as BaseNotificationsConfig;

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
     * {@inheritDoc}
     */
    public function getBackup(): BackupConfig
    {
        return new DatabaseBackupConfigProvider($this->original->backup);
    }

    /**
     * {@inheritDoc}
     */
    public function getNotifications(): BaseNotificationsConfig
    {
        $provider = app(NotificationConfigurationProviderInterface::class);

        return NotificationsConfig::fromProvider($provider);
    }

    /**
     * {@inheritDoc}
     */
    public function getMonitoredBackups(): MonitoredBackupsConfig
    {
        return $this->original->monitoredBackups;
    }

    /**
     * {@inheritDoc}
     */
    public function getCleanup(): CleanupConfig
    {
        return $this->original->cleanup;
    }
}
