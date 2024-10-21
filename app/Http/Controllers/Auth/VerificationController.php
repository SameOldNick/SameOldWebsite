<?php

namespace App\Http\Controllers\Auth;

use App\Components\SweetAlert\Swal;
use App\Components\SweetAlert\SweetAlertBuilder;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails {
        resend as originalResend;
    }

    /**
     * Where to redirect users after verification.
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
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function resend(Request $request)
    {
        Swal::success(function (SweetAlertBuilder $builder) {
            $builder
                ->title(__('Resent'))
                ->content(__('A fresh verification link has been sent to your email address.'), false);
        });

        return $this->originalResend($request);
    }

    /**
     * {@inheritDoc}
     */
    protected function verified(Request $request)
    {
        Swal::success(function (SweetAlertBuilder $builder) {
            $builder
                ->title(__('Verified'))
                ->content(__('auth.verified'), false);
        });

        return redirect($this->redirectPath())->with('verified', true);
    }
}
