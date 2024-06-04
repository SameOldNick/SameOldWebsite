<?php

namespace App\Components\MFA\Exceptions;

use Exception;
use Illuminate\Http\Request;

/**
 * Exception for when a user needs MFA.
 */
final class MFARequiredException extends Exception
{
    protected $mfaPath;

    /**
     * Intializes exception
     *
     * @param  string  $mfaPath  Path to the MFA prompt.
     */
    public function __construct(string $mfaPath)
    {
        parent::__construct('Multi-Factor Authentication is required.');

        $this->mfaPath = $mfaPath;
    }

    /**
     * Gets the path to the MFA prompt.
     */
    public function getMfaPath(): string
    {
        return $this->mfaPath;
    }

    /**
     * Creates response for when exception is handled.
     *
     * @return mixed
     */
    public function render(Request $request)
    {
        // Stores intended URL in session
        return redirect()->guest($this->getMfaPath());
    }

    /**
     * Throws the exception.
     *
     * @param  string  $mfaPath  Path to MFA prompt
     */
    public static function throw(string $mfaPath): static
    {
        throw new self($mfaPath);
    }
}
