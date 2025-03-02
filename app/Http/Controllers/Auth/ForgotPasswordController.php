<?php

namespace App\Http\Controllers\Auth;

use App\Components\Captcha\Facades\Captcha;
use App\Components\Captcha\Rules\CaptchaRule;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Validate the email for the given request.
     *
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $rules = ['email' => 'required|email'];

        if (Captcha::getDriver('recaptcha')->isReady()) {
            $rules['g-recaptcha-response'] = CaptchaRule::required('recaptcha');
        }

        $request->validate($rules);
    }
}
