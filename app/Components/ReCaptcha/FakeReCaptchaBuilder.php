<?php

namespace App\Components\ReCaptcha;

use Biscolab\ReCaptcha\ReCaptchaBuilder;
use Illuminate\Support\Str;

class FakeReCaptchaBuilder extends ReCaptchaBuilder {
    /**
     * Whether actual builder should be used.
     *
     * @var boolean
     */
    protected bool $actual = false;

    /**
     * Allowed fake responses
     *
     * @var array
     */
    protected array $allowed = [];

    /**
     * Previous responses
     *
     * @var array
     */
    protected array $responses = [];

    /**
     * Enables actual ReCapthca checker
     *
     * @return static
     */
    public function useActual(): static {
        $this->actual = true;

        return $this;
    }

    /**
     * Enables fake ReCpatcha checker
     *
     * @return static
     */
    public function useFake(): static {
        $this->actual = false;

        return $this;
    }

    /**
     * Gets if actual ReCaptcha checker should be used.
     *
     * @return boolean
     */
    public function actual(): bool {
        return $this->actual;
    }

    /**
     * Gets if fake ReCaptcha checker should be used.
     *
     * @return boolean
     */
    public function faked(): bool {
        return !$this->actual();
    }

    /**
     * Generates and stores valid fake ReCaptcha response.
     *
     * @return string
     */
    public function validResponse(): string {
        $response = (string) Str::uuid();

        array_push($this->allowed, $response);

        return $response;
    }

    /**
     * Checks if response is allowed.
     *
     * @param string $response
     * @return boolean
     */
    public function isAllowed(string $response): bool {
        return in_array($response, $this->allowed);
    }

    /**
     * Call out to reCAPTCHA and process the response
     *
     * @param string $response
     *
     * @return boolean|array
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
     *
     * @return array
     */
    public function getResponses(): array {
        return $this->responses;
    }

    /**
     * Gets faked successful ReCaptcha response
     *
     * @param boolean $asArray If true, an array is returned.
     * @return array|boolean
     */
    protected function getFakedResponseSuccess(bool $asArray) {
        return $asArray ? [
            'faked'      => true,
            'score'      => 0.9,
            'success'    => true
        ] : true;
    }

    /**
     * Gets faked failed ReCaptcha response
     *
     * @param boolean $asArray If true, an array is returned.
     * @return array|boolean
     */
    protected function getFakedResponseFail(bool $asArray) {
        return $asArray ? [
            'faked'      => true,
            'error'      => 'The ReCaptcha validation failed.',
            'score'      => 0.1,
            'success'    => false
        ] : false;
    }

    /**
     * Creates fake ReCaptchaBuilder from existing ReCaptchaBuilder
     *
     * @param ReCaptchaBuilder $reCaptchaBuilder
     * @return self
     */
    public static function createFromBase(ReCaptchaBuilder $reCaptchaBuilder): self {
        return new self($reCaptchaBuilder->getApiSiteKey(), $reCaptchaBuilder->getApiSecretKey(), $reCaptchaBuilder->getVersion());
    }
}
