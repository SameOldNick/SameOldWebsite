<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Displays the user
     *
     * @return User
     */
    public function show(Request $request)
    {
        return $request->user();
    }
}
