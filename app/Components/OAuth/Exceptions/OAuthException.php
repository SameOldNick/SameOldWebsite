<?php

namespace App\Components\OAuth\Exceptions;

use Exception;
use Illuminate\Http\Response;

class OAuthException extends Exception
{
    /**
     * Render the exception.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render()
    {
        return new Response('');
    }
}
