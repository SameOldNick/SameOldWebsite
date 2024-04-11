<?php

namespace App\Components\Security;

use App\Components\Security\Responders\EventResponder\EventResponder;
use App\Components\Security\Responders\StackResponder;
use Illuminate\Support\Arr;
use Illuminate\Support\Manager;

final class Responder extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string|null
     */
    public function getDefaultDriver()
    {
        $key = config('security.responder');

        if (is_null($key)) {
            return null;
        }

        return $this->determineDriverNameFor($key);
    }

    /**
     * Creates stack responder.
     *
     * @return StackResponder
     */
    protected function createStackDriver()
    {
        $keys = Arr::get($this->getConfig('stack'), 'stack', []);

        $stack = array_map(function ($key) {
            $name = $this->determineDriverNameFor($key);

            if (! is_null($name)) {
                return $this->driver($name);
            }

            return null;
        }, $keys);

        return new StackResponder(array_filter($stack));
    }

    /**
     * Creates even responder.
     *
     * @return EventResponder
     */
    protected function createEventDriver()
    {
        return new EventResponder();
    }

    /**
     * Gets configuration for responder.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function getConfig(string $key, $default = [])
    {
        return config(sprintf('security.responders.%s', $key), $default);
    }

    /**
     * Determines driver name for responder.
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
