<?php

namespace App\Rules;

use App\Models\Country;
use App\Traits\Rules\DataAware;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class State implements DataAwareRule, Rule
{
    use DataAware;

    /**
     * Key that contains 3 letter country code.
     *
     * @var string
     */
    protected $countryKey;

    /**
     * Create a new rule instance.
     *
     * @param  string  $countryKey  Key that contains 3 letter country code (can be dot notation).
     * @return void
     */
    public function __construct($countryKey)
    {
        $this->countryKey = $countryKey;
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
        $countryCode = Arr::get($this->data, $this->countryKey);
        $country = Country::find($countryCode);

        /**
         * If null, this means the country code doesn't exist so fail.
         */
        if (is_null($country)) {
            return false;
        }

        $states = $country->states;

        if ($states->count() > 0) {
            // Just passing $value as a parameter to contains() won't work cause 'id' is the primary key column.
            return $states->contains(fn($model) => $model->code == $value);
        }

        // No states for country exist, but require field to be empty.
        return empty($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The state is not valid for that country.';
    }
}
