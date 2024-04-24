<?php

namespace App\Components\Fakers\Providers;

use App\Components\Passwords\Generator\Generator;
use App\Components\Passwords\Generator\Options;
use App\Components\Passwords\Password;
use Faker\Provider\Base;
use Illuminate\Support\Arr;

class Security extends Base
{
    public function strongPassword(?Options $options = null)
    {
        return Password::default()->generate($options);
    }
}
