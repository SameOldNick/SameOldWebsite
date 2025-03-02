<?php

namespace App\Http\Controllers\Auth;

use App\Components\Captcha\Facades\Captcha;
use App\Components\Captcha\Rules\CaptchaRule;
use App\Components\SweetAlert\Swal;
use App\Components\SweetAlert\SweetAlertBuilder;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Controllers\ReturnsToUrl;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;
    use ReturnsToUrl;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(Request $request)
    {
        $data = [];
        $returnUrl = $request->get('return_url');

        if (is_string($returnUrl)) {
            $data['returnUrl'] = $returnUrl;
        }

        return view('auth.register', $data);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => Password::required(),
            'country' => ['required', 'string', 'exists:countries,code'],
            'terms_conditions' => ['required', 'accepted'],
        ];

        if (Captcha::getDriver('recaptcha')->isReady()) {
            $rules['g-recaptcha-response'] = CaptchaRule::required('recaptcha');
        }

        $messages = [
            'terms_conditions.accepted' => 'You must accept the terms and conditions.',
        ];

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'country_code' => $data['country'],
        ]);
    }

    /**
     * The user has been registered.
     *
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        $returnResponse = $this->returnToSafeResponse($request);

        if (! is_null($returnResponse)) {
            return $returnResponse;
        }

        Swal::success(function (SweetAlertBuilder $builder) {
            $builder
                ->title(__('Registered'))
                ->content(__('auth.registered'), false);
        });
    }

    /**
     * Where to redirect users after registration.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return route('user.profile');
    }
}
