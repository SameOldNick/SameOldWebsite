<?php

namespace App\Components\LittleJWT;

use Illuminate\Contracts\Container\Container;
use LittleApps\LittleJWT\Guards\Adapters\AbstractAdapter;
use LittleApps\LittleJWT\Guards\Adapters\Concerns\BuildsJwt;
use LittleApps\LittleJWT\Validation\Validatables\StackValidatable;

class RefreshTokenGuardAdapter extends AbstractAdapter
{
    use BuildsJwt;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->jwt = $container->make('littlejwt.refresh');

        $this->config = [];
    }

    /**
     * Gets a callback that receives a Validator to specify the JWT validations.
     *
     * @return callable
     */
    protected function getValidatorCallback()
    {
        $validatable = new StackValidatable([
            $this->container->make('littlejwt.validatables.guard'),
            new RefreshTokenValidatable(),
        ]);

        return [$validatable, 'validate'];
    }
}
