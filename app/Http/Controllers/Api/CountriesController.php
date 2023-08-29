<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;

class CountriesController extends Controller
{
    public function countries()
    {
        return Country::all();
    }

    public function country(Country $country)
    {
        return $country;
    }
}
