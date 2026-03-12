<?php

namespace App\Http\Controllers\Auth;

use App\Components\Captcha\Facades\Captcha;
use App\Components\Captcha\Rules\CaptchaRule;
use App\Http\Controllers\Controller;
use App\Http\Middleware\EncryptCookies;
use App\Providers\RouteServiceProvider;
use App\Traits\Controllers\ReturnsToUrl;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use LittleApps\LittleJWT\Facades\Blacklist;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
    use ReturnsToUrl;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout', 'apiLogout');

        $this->middleware([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            EnsureFrontendRequestsAreStateful::class,
        ])->only('apiLogout');
    }

    /**
     * Logs user out by invalidating session and blacklisting JWT
     *
     * @return mixed
     */
    public function apiLogout(Request $request)
    {
        // Blacklist the JWT
        Blacklist::blacklist($request->getJwt());

        return $this->logout($request);
    }

    /**
     * The user has logged out of the application.
     *
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        return $this->returnToSafeResponse($request);
    }

    /**
     * Show the application's login form.
     *
     * @return View
     */
    public function showLoginForm(Request $request)
    {
        $data = [];
        $returnUrl = $request->string('return_url');

        if ($returnUrl->isNotEmpty()) {
            $data['returnUrl'] = $returnUrl;
        }

        return view('auth.login', $data);
    }

    /**
     * Validate the user login request.
     *
     * @return void
     *
     * @throws ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $rules = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        if (Captcha::getDriver('recaptcha')->isReady()) {
            $rules['g-recaptcha-response'] = CaptchaRule::required('recaptcha');
        }

        $request->validate($rules);
    }

    /**
     * The user has been authenticated.
     *
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        return $this->returnToSafeResponse($request);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('web');
    }
}
