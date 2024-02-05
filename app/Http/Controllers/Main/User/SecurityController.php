<?php

namespace App\Http\Controllers\Main\User;

use App\Components\MFA\Concerns\UsesMultiFactorAuthenticator;
use App\Components\MFA\Facades\MFA;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\OneTimePasscode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\MFA\Services\Authenticator\Drivers\OneTimePasscode\OneTimeAuthenticatable;
use Illuminate\Support\Facades\Crypt;

class SecurityController extends Controller
{

    /**
     * Displays change password page
     *
     * @return mixed
     */
    public function view(Request $request)
    {
        $configured = MFA::isConfigured($request->user());

        return view('main.user.security', ['configured' => $configured]);
    }
}
