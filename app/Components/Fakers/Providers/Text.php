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

    /**
     * Generates a profane word.
     *
     * @param  string  $lang
     * @return string
     */
    public function profanity(int $count = 1, $lang = 'en')
    {
        $words = config("profanity.{$lang}", []);

        return $count === 1 ? $this->randomElement($words) : $this->randomElements($words, $count);
    }
}
