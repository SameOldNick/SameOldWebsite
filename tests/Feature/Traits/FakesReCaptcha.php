<?php

namespace Tests\Feature\Traits;

use App\Components\ReCaptcha\FakeReCaptchaBuilder;
use Biscolab\ReCaptcha\ReCaptchaBuilder;
use Tests\TestCase;

/**
 * @mixin TestCase
 */
trait FakesReCaptcha
{
    /**
     * Sets up fake recaptcha
     *
     * @return void
     */
    public function setUpFakesReCaptcha()
    {
        $this->app->extend('recaptcha', function (ReCaptchaBuilder $reCaptchaBuilder) {
            return FakeReCaptchaBuilder::createFromBase($reCaptchaBuilder);
        });
    }

    /**
     * Tears down fake recaptcha
     *
     * @return void
     */
    public function tearDownFakesReCaptcha()
    {
    }

    /**
     * Asserts a recaptcha response exists
     *
     * @return static
     */
    public function assertHasReCaptchaResponse(): static {
        $this->assertNotEmpty($this->getReCaptchaResponses(), 'There are no ReCaptcha responses.');

        return $this;
    }

    /**
     * Asserts a recaptcha response is missing
     *
     * @return static
     */
    public function assertMissingReCaptchaResponse(): static {
        $this->assertEmpty($this->getReCaptchaResponses(), 'There are ReCaptcha responses.');

        return $this;
    }

    /**
     * Asserts the last recaptcha response was successful
     *
     * @return static
     */
    public function assertLastReCaptchaResponseSuccessful(): static {
        $this->assertReCaptchaResponseSuccessful($this->getLastReCaptchaResponse(), 'The last ReCaptcha response was not successful.');

        return $this;
    }

    /**
     * Asserts all recaptcha responses were successful
     *
     * @return static
     */
    public function assertReCaptchaResponsesSuccessful(): static {
        foreach ($this->getReCaptchaResponses() as $response) {
            $this->assertReCaptchaResponseSuccessful($response);
        }

        return $this;
    }

    /**
     * Asserts last recaptcha responses failed
     *
     * @return static
     */
    public function assertLastReCaptchaResponseFailed(): static {
        $this->assertReCaptchaResponseFailed($this->getLastReCaptchaResponse(), 'The last ReCaptcha response was not successful.');

        return $this;
    }

    /**
     * Asserts all recapatcha responses failed
     *
     * @return static
     */
    public function assertReCaptchaResponsesFailed(): static {
        foreach ($this->getReCaptchaResponses() as $response) {
            $this->assertReCaptchaResponseFailed($response);
        }

        return $this;
    }

    /**
     * Asserts recaptcha response was successful
     *
     * @param array|bool $response ReCaptcha response
     * @param string $message Message to display if assertion fails. (default: empty string)
     * @return static
     */
    public function assertReCaptchaResponseSuccessful($response, string $message = ''): static {
        $this->assertTrue($this->isReCaptchaResponseSuccessful($response), $message ?: 'The ReCaptcha response was not successful.');

        return $this;
    }

    /**
     * Asserts recaptcha response failed
     *
     * @param array|bool $response ReCaptcha response
     * @param string $message Message to display if assertion fails. (default: empty string)
     * @return static
     */
    public function assertReCaptchaResponseFailed($response, string $message = ''): static {
        $this->assertFalse($this->isReCaptchaResponseSuccessful($response), $message ?: 'The ReCaptcha response was successful.');

        return $this;
    }

    /**
     * Checks if recaptcha response is successful.
     *
     * @param array|bool $response
     * @return boolean
     */
    protected function isReCaptchaResponseSuccessful($response): bool {
        return (bool) isset($response['success']) ? $response['success'] : $response;
    }

    /**
     * Gets previous recaptcha responses
     *
     * @return array
     */
    protected function getReCaptchaResponses(): array {
        return $this->app['recaptcha']->getResponses();
    }

    /**
     * Gets last recaptcha response
     *
     * @return array|bool
     */
    protected function getLastReCaptchaResponse() {
        return array_pop($this->getReCaptchaResponses());
    }
}
