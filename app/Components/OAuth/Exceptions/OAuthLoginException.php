<?php

namespace App\Components\OAuth\Exceptions;

use App\Components\SweetAlert\SweetAlertBuilder;
use App\Components\SweetAlert\SweetAlerts;
use Exception;
use Illuminate\Support\Facades\Auth;

class OAuthLoginException extends OAuthException
{
    public function __construct(public ?Exception $original = null) {}

    /**
     * Render the exception.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render()
    {
        $message = __('An unknown error occurred. Please try again.');

        if (! is_null($this->original) && ! app()->isProduction()) {
            if ($this->original->getMessage()) {
                $message = sprintf('Exception "%s" was thrown: %s', get_class($this->original), $this->original->getMessage());
            } else {
                $message = sprintf('Exception "%s" was thrown', get_class($this->original));
            }
        }

        if (Auth::check()) {
            app(SweetAlerts::class)->success(function (SweetAlertBuilder $builder) use ($message) {
                $builder
                    ->title('Ooops...')
                    ->icon('danger')
                    ->text($message);
            });

            return redirect()->back();
        } else {
            return redirect()->route('login')->withErrors(['oauth' => [$message]]);
        }
    }
}
