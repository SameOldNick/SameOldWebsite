<?php

namespace App\Components\Captcha\Rules;

use App\Components\Captcha\Contracts\Driver;
use App\Components\Captcha\Facades\Captcha;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

class CaptchaRule implements ValidationRule
{
    protected static array $validResponses = [];

    protected static array $invalidResponses = [];

    /**
     * Creates a new captcha rule.
     */
    public function __construct(
        private readonly ?string $driver = null,
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            if (Captcha::isFake()) {
                if (in_array($value, static::$validResponses)) {
                    return;
                } elseif (in_array($value, static::$invalidResponses)) {
                    throw new Exception('Captcha verification failed');
                }
            }

            $this->getDriver()->verifier()->validateRule($attribute, $value);
        } catch (Exception $ex) {
            $fail(! app()->isProduction() ? $ex->getMessage() : $this->defaultMessage());
        }
    }

    /**
     * Get the default validation error message.
     */
    public function defaultMessage(): string
    {
        return __('You appear to be a robot.');
    }

    /**
     * Get the captcha driver.
     */
    public function getDriver(): Driver
    {
        return Captcha::getDriver($this->driver);
    }

    /**
     * Creates a new required rule.
     *
     * @param  array  $rules  Any additional rules
     */
    public static function required(?string $driver = null, array $rules = []): array
    {
        return ['required', new static($driver), ...$rules];
    }

    /**
     * Creates a new valid response.
     *
     * @return string The response code
     */
    public static function validResponse(?string $responseCode = null): string
    {
        $responseCode = $responseCode ?? fake()->uuid;

        static::$validResponses[] = $responseCode;

        return $responseCode;
    }

    /**
     * Creates a new invalid response.
     *
     * @return string The response code
     */
    public static function invalidResponse(?string $responseCode = null): string
    {
        $responseCode = $responseCode ?? fake()->uuid;

        static::$invalidResponses[] = $responseCode;

        return $responseCode;
    }
}
