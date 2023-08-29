<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Displays the user
     *
     * @param Request $request
     * @return \App\Models\User
     */
    public function show(Request $request)
    {
        return $request->user();
    }


}
