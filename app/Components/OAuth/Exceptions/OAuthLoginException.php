<?php

namespace App\Components\OAuth\Exceptions;

use Exception;

class OAuthLoginException extends OAuthException {
    public function __construct(public ?Exception $original = null)
    {

    }

    /**
     * Render the exception.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        $message = __('An unknown error occurred. Please try again.');

        if (!is_null($this->original) && !app()->isProduction()) {
            if ($this->original->getMessage()) {
                $message = sprintf('Exception "%s" was thrown: %s', get_class($this->original), $this->original->getMessage());
            } else {
                $message = sprintf('Exception "%s" was thrown', get_class($this->original));
            }
        }

        return redirect()->route('login')->withErrors(['oauth' => [$message]]);
    }
}
