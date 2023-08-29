<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\State;
use App\Models\Country;

class CountriesController extends Controller
{
    public function countries() {
        return Country::all();
    }

    public function country(Country $country) {
        return $country;
    }
}
