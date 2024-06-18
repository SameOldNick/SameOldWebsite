<?php

namespace App\Components\Fakers\Providers;

use App\Components\Passwords\Generator\Options;
use App\Components\Passwords\Password;
use Faker\Provider\Base;

class Text extends Base
{
    /**
     * Generates a strong password.
     *
     * @return string
     */
    public function strongPassword(?Options $options = null)
    {
        return Password::default()->generate($options);
    }
}
