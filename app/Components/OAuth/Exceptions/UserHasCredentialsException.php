<?php

namespace App\Components\OAuth\Exceptions;

class UserHasCredentialsException extends OAuthException {
    /**
     * Render the exception.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return redirect()->route('login')->with('info', __('A user with that e-mail address already exists. Please sign-in with a password to continue.'));
    }
}
