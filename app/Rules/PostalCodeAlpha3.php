<?php

namespace App\Rules;

use App\Models\Country;
use Illuminate\Support\Arr;
use Illuminate\Validation\NestedRules;

class PostalCodeAlpha3 extends NestedRules
{
    protected $countryKey;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($countryKey = null)
    {
        parent::__construct([$this, 'createRule']);

        $this->countryKey = $countryKey ?? 'country';
    }

    public function createRule($value, $attribute, $data)
    {
        $countryCodeAlpha3 = Arr::get($data, $this->countryKey, '');
        $country = Country::find($countryCodeAlpha3);

        /**
         * Other rules should ensure country with code exists.
         * If it some how reaches here without a country, bail (causing validation to fail).
         */
        if (is_null($country)) {
            return 'bail';
        }

        // Must be an array (seperating string with : doesn't work for some reason)
        return ['postal_code', $country->code_alpha2];
    }
}
