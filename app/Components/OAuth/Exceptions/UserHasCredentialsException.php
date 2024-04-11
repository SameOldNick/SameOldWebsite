<?php

namespace App\Components\OAuth\Exceptions;

use App\Models\User;
use Exception;

class UserHasCredentialsException extends OAuthException
{
    public function __construct(
        public User $user,
        ?Exception $original = null
    ) {
        parent::__construct($original);
    }

    /**
     * Render the exception.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render()
    {
        return
            redirect()
                ->route('login')
                    ->with('info', __('A user with that e-mail address already exists. Please sign-in with a password to continue.'))
                    ->withInput(['email' => $this->user->email]);
    }
}
