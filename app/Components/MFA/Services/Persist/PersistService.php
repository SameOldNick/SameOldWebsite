<?php

namespace App\Components\MFA\Services\Persist;

use Illuminate\Support\Manager;

class PersistService extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return 'session';
    }

    /**
     * Creates session driver.
     *
     * @return SessionDriver
     */
    protected function createSessionDriver()
    {
        $manager = $this->container->make('session');
        $config = $this->container->make('config')->get('mfa.persist.drivers.session', []);

        return new Drivers\SessionDriver($manager, $config);
    }
}
