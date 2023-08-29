<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UncommonPassword implements Rule
{
    protected $commonPasswords;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->commonPasswords = collect(config('passwords.weak', []));
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return ! $this->commonPasswords->contains(function ($password) use ($value) {
            return stripos($value, $password) !== false;
        });
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Password cannot contain a commonly used word.';
    }
}
