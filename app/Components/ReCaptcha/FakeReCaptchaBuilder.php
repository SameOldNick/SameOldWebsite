<?php

namespace App\Components\ReCaptcha;

use Biscolab\ReCaptcha\ReCaptchaBuilder;
use Illuminate\Support\Str;

class FakeReCaptchaBuilder extends ReCaptchaBuilder
{
    /**
     * Whether faked builder should be used.
     */
    protected bool $faked = false;

    /**
     * Allowed fake responses
     */
    protected array $allowed = [];

    /**
     * Previous responses
     */
    protected array $responses = [];

    /**
     * Enables fake ReCpatcha checker
     */
    public function fake(bool $enabled = true): static
    {
        $this->faked = $enabled;

        return $this;
    }

    /**
     * Gets if actual ReCaptcha checker should be used.
     */
    public function actual(): bool
    {
        return !$this->faked();
    }

    /**
     * Gets if fake ReCaptcha checker should be used.
     */
    public function faked(): bool
    {
        return $this->faked;
    }

    /**
     * Generates and stores valid fake ReCaptcha response.
     */
    public function validResponse(): string
    {
        $response = (string) Str::uuid();

        array_push($this->allowed, $response);

        return $response;
    }

    /**
     * Checks if response is allowed.
     */
    public function isAllowed(string $response): bool
    {
        return in_array($response, $this->allowed);
    }

    /**
     * Call out to reCAPTCHA and process the response
     *
     * @param  string  $response
     * @return bool|array
     */
    public function validate($response)
    {
        if ($this->actual()) {
            $response = parent::validate($response);
        } else {
            $response = $this->isAllowed($response) ? $this->getFakedResponseSuccess($this->returnArray()) : $this->getFakedResponseFail($this->returnArray());
        }

        array_push($this->responses, $response);

        return $response;
    }

    /**
     * Gets previous ReCaptcha validation responses.
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    /**
     * Gets faked successful ReCaptcha response
     *
     * @param  bool  $asArray  If true, an array is returned.
     * @return array|bool
     */
    protected function getFakedResponseSuccess(bool $asArray)
    {
        return $asArray ? [
            'faked' => true,
            'score' => 0.9,
            'success' => true,
        ] : true;
    }

    /**
     * Gets faked failed ReCaptcha response
     *
     * @param  bool  $asArray  If true, an array is returned.
     * @return array|bool
     */
    protected function getFakedResponseFail(bool $asArray)
    {
        return $asArray ? [
            'faked' => true,
            'error' => 'The ReCaptcha validation failed.',
            'score' => 0.1,
            'success' => false,
        ] : false;
    }

    /**
     * Creates fake ReCaptchaBuilder from existing ReCaptchaBuilder
     */
    public static function createFromBase(ReCaptchaBuilder $reCaptchaBuilder): self
    {
        return new self($reCaptchaBuilder->getApiSiteKey(), $reCaptchaBuilder->getApiSecretKey(), $reCaptchaBuilder->getVersion());
    }
}
