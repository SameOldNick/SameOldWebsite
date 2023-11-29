<?php

namespace App\Components\Security;

use App\Components\Security\Clerks\EloquentClerk\EloquentClerk;
use App\Components\Security\Clerks\NotificationClerk\NotificationClerk;
use App\Components\Security\Clerks\StackClerk;
use Illuminate\Support\Arr;
use Illuminate\Support\Manager;

final class Clerk extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        $key = config('security.clerk');

        if (is_null($key)) {
            return null;
        }

        return $this->determineDriverNameFor($key);
    }

    /**
     * Creates stack clerk.
     *
     * @return StackClerk
     */
    protected function createStackDriver()
    {
        $stack = Arr::get($this->getConfig('stack'), 'stack', []);

        $drivers = array_map(fn ($name) => $this->driver($name), $stack);

        return new StackClerk($drivers);
    }

    /**
     * Creates eloquent clerk.
     *
     * @return void
     */
    protected function createEloquentDriver()
    {
        return new EloquentClerk($this->getConfig('eloquent'));
    }

    /**
     * Creates notification clerk
     *
     * @return NotificationClerk
     */
    protected function createNotificationDriver()
    {
        $config = $this->getConfig('notification');

        return new NotificationClerk($config);
    }

    /**
     * Gets configuration for clerk.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function getConfig(string $key, $default = [])
    {
        return config(sprintf('security.clerks.%s', $key), $default);
    }

    /**
     * Determines driver name for clerk.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function determineDriverNameFor(string $key, $default = null)
    {
        return Arr::get($this->getConfig($key), 'driver', $default);
    }
}
