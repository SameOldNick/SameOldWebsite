<?php

namespace App\Components\Backup;

use App\Components\Backup\Contracts\BackupSchedulerConfigurationProvider;
use Cron\CronExpression;
use Illuminate\Support\Facades\Schedule;

class BackupScheduler
{
    /**
     * Initializes backup scheduler
     *
     * @param BackupSchedulerConfigurationProvider $configurationProvider
     */
    public function __construct(
        public readonly BackupSchedulerConfigurationProvider $configurationProvider
    ) {}

    /**
     * Schedules backup and cleanup commands
     *
     * @return void
     */
    public function schedule()
    {
        if ($this->configurationProvider->isBackupEnabled()) {
            $this->scheduleBackup();
        }

        if ($this->configurationProvider->isCleanupEnabled()) {
            $this->scheduleCleanup();
        }
    }

    /**
     * Schedules backup command
     *
     * @return void
     */
    public function scheduleBackup()
    {
        $expression = $this->transformCronExpression($this->configurationProvider->getBackupCronExpression());

        $this->scheduleCommand('backup:run', $expression);
    }

    /**
     * Schedules cleanup command
     *
     * @return void
     */
    public function scheduleCleanup()
    {
        $expression = $this->transformCronExpression($this->configurationProvider->getCleanupCronExpression());

        $this->scheduleCommand('backup:clean', $expression);
    }

    /**
     * Transforms Cron alias to expression
     * This is because Laravel doesn't play well with aliases like @yearly, @monthly, etc.
     *
     * @param string $expression
     * @return string
     */
    protected function transformCronExpression(string $expression): string
    {
        $aliases = CronExpression::getAliases();

        return isset($aliases[$expression]) ? $aliases[$expression] : $expression;
    }

    /**
     * Schedules command
     *
     * @param string $command
     * @param string $expression
     * @return \Illuminate\Console\Scheduling\Event
     */
    protected function scheduleCommand(string $command, string $expression)
    {
        return Schedule::command($command)->cron($expression);
    }
}
