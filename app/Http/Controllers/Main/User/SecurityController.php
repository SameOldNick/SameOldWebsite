<?php

namespace App\Http\Controllers\Main\User;

use App\Components\MFA\Facades\MFA;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    /**
     * Displays change password page
     *
     * @return View
     */
    public function view(Request $request)
    {
        $configured = MFA::isConfigured($request->user());

        return view('main.user.security', ['configured' => $configured]);
    }
}
