<?php

namespace App\Components\ReCaptcha;

use Biscolab\ReCaptcha\Facades\ReCaptcha;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ReCaptchaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $response = ReCaptcha::validate($value);

        // The original doesn't check array response correctly.
        if (!$this->isResponseSuccessful($response)) {
            $message = !config('recaptcha.empty_message') ? trans(config('recaptcha.error_message_key')) : null;

            $fail($message);
        }
    }

    /**
     * Checks if response is successful.
     *
     * @param array|bool $response
     * @return boolean
     */
    protected function isResponseSuccessful($response): bool {
        return (bool) isset($response['success']) ? $response['success'] : $response;
    }
}
