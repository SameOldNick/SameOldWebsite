<?php

namespace App\Components\Security;

use App\Components\Security\Watchdogs\ComposerAuditWatchdog;
use App\Components\Security\Watchdogs\HttpSecureWatchdog;
use App\Components\Security\Watchdogs\StackWatchdog;
use Illuminate\Support\Arr;
use Illuminate\Support\Manager;

final class Watchdog extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        $key = config('security.watchdog');

        if (is_null($key))
            return null;

        return $this->determineDriverNameFor($key);
    }

    /**
     * Creates stack watchdog.
     *
     * @return StackWatchdog
     */
    protected function createStackDriver()
    {
        $stack = Arr::get($this->getConfig('stack'), 'stack', []);

        $drivers = array_map(fn ($name) => $this->driver($name), $stack);

        return new StackWatchdog($drivers);
    }

    /**
     * Creates composer audit watchdog.
     *
     * @return ComposerAuditWatchdog
     */
    protected function createComposerAuditDriver()
    {
        return $this->container->make(ComposerAuditWatchdog::class, ['config' => $this->getConfig('composer-audit')]);
    }

    /**
     * Creates HTTPS watchdog.
     *
     * @return HttpSecureWatchdog
     */
    protected function createHttpsDriver()
    {
        return $this->container->make(HttpSecureWatchdog::class, ['config' => $this->getConfig('https')]);
    }

    /**
     * Gets config for watchdog.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function getConfig(string $key, $default = []) {
        return config(sprintf('security.watchdogs.%s', $key), $default);
    }

    /**
     * Determines driver name for watchdog.
     *
     * @param string $key
     * @param mixed $default
     * @return string
     */
    private function determineDriverNameFor(string $key, $default = null) {
        return Arr::get($this->getConfig($key), 'driver', $default);
    }
}
