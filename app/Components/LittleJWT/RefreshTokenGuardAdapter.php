<?php

namespace App\Components\LittleJWT;

use Illuminate\Contracts\Container\Container;
use LittleApps\LittleJWT\Guards\Adapters\AbstractAdapter;
use LittleApps\LittleJWT\Guards\Adapters\Concerns\BuildsJwt;
use LittleApps\LittleJWT\Validation\ExtendedValidator;

class RefreshTokenGuardAdapter extends AbstractAdapter
{
    use BuildsJwt;

    /**
     * Initializes adapter instance
     *
     * @param Container $container
     * @param array $config
     */
    public function __construct(Container $container, array $config = [])
    {
        parent::__construct($container, $config);
    }

    /**
     * Gets the LittleJWT handler
     *
     * @return \LittleApps\LittleJWT\Core\Handler
     */
    protected function getHandler()
    {
        return $this->container->make('littlejwt.refresh')->handler();
    }

    /**
     * Gets a callback that receives a Validator to specify the JWT validations.
     *
     * @return callable
     */
    protected function getValidatorCallback()
    {
        return function (ExtendedValidator $validator) {
            $validator
                ->with($this->container->make('littlejwt.validatables.guard'))
                ->with(new RefreshTokenValidatable());
        };
    }
}
