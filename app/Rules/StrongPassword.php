<?php

namespace App\Rules;

use App\Traits\Rules\AggregateValidators;
use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
{
    use AggregateValidators;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Gets validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return config('passwords.rules', []);
    }

    /**
     * Gets messages for validation.
     *
     * @return array
     */
    public function messages()
    {
        return config('passwords.messages', []);
    }
}
