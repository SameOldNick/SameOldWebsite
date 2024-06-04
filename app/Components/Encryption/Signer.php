<?php

namespace App\Components\Encryption;

use App\Components\Encryption\Signers\EcdsaSigner;
use Illuminate\Support\Manager as BaseManager;

class Signer extends BaseManager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return config('encryption.defaults.signer');
    }

    /**
     * Creates ECDSA driver
     *
     * @return EcdsaSigner
     */
    protected function createEcdsaDriver()
    {
        $config = $this->getConfigFor('ecdsa');

        return new EcdsaSigner($config);
    }

    /**
     * Gets configuration for driver.
     *
     * @return array
     */
    protected function getConfigFor(string $name)
    {
        return config("encryption.drivers.{$name}");
    }
}
