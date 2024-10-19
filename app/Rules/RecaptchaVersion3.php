<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Biscolab\ReCaptcha\Facades\ReCaptcha;

/**
 * This fixes Recaptcha v3 validation from the biscolab/laravel-recaptcha package.
 * The biscolab/laravel-recaptcha implementation of v3 seems to be incomplete.
 * Rather than associating the token with the form, it just sends it to a controller.
 * The validation rule in biscolab/laravel-recaptcha also doesn't work for v3.
 * That is because the validate method returns an array when it's in v3 mode,
 * which the Laravel validator expects a boolean and ends up considering the non-empty
 * array as true.
 */
class RecaptchaVersion3 implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The :attribute value must be a string.');
            return;
        }

        $result = ReCaptcha::validate($value);

        if ($this->isSuccessful($result)) {
            return;
        }

        $fail($this->getMessage($result));
    }

    /**
     * Checks if validate response is successful
     *
     * @param array|bool $result
     * @return boolean
     */
    protected function isSuccessful($result): bool
    {
        if (is_bool($result))
            return $result;

        return $result['success'];
    }

    /**
     * Translates validate response to message
     *
     * @param array|bool $result
     * @return string
     */
    protected function getMessage($result): string
    {
        $default = 'You appear to be a robot.';

        if (is_array($result)) {
            if (app()->isProduction()) {
                $defaultServerSide = 'Something went wrong on our end. Please try again.';

                $errorMapping = [
                    'missing-input-secret' => $defaultServerSide,
                    'invalid-input-secret' => $defaultServerSide,
                    'missing-input-response' => $default,
                    'invalid-input-response' => $default,
                    'bad-request' => $defaultServerSide,
                    'timeout-or-duplicate' => $default,
                ];
            } else {
                $errorMapping = [
                    'missing-input-secret' => 'The secret parameter is missing.',
                    'invalid-input-secret' => 'The secret parameter is invalid or malformed.',
                    'missing-input-response' => 'The response parameter is missing.',
                    'invalid-input-response' => 'The response parameter is invalid or malformed.',
                    'bad-request' => 'The request is invalid or malformed.',
                    'timeout-or-duplicate' => 'The response is no longer valid: either is too old or has been used previously.',
                ];
            }

            $errorCodes = !empty($result['error-codes']) ? $result['error-codes'] : [];

            foreach ($errorCodes as $errorCode) {
                if (isset($errorMapping[$errorCode]))
                    return $errorMapping[$errorCode];
            }
        }

        return $default;
    }
}
