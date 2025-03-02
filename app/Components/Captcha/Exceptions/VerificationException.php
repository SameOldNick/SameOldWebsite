<?php

namespace App\Components\Captcha\Exceptions;

use Exception;
use Throwable;

class VerificationException extends Exception
{
    /**
     * Create a new instance.
     *
     * @param  string  $message
     * @param  int  $code
     */
    public function __construct($message = 'The CAPTCHA verification failed.', $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create a new instance with a reason.
     *
     * @param  int  $code
     * @return static
     */
    public static function withReason(?string $reason = null, $code = 0, ?Throwable $previous = null): self
    {
        return new self(
            $reason ? __('Captcha verification failed: :reason', ['reason' => $reason]) : __('Captcha verification failed')
        );
    }
}
