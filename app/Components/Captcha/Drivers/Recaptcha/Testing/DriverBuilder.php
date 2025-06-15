<?php

namespace App\Components\Captcha\Drivers\Recaptcha\Testing;

use App\Components\Captcha\Contracts\Driver as DriverContract;
use App\Components\Captcha\Drivers\Recaptcha\Presenter;
use App\Components\Captcha\Drivers\Recaptcha\Testing\Verifier as TestingVerifier;
use App\Components\Captcha\Drivers\Recaptcha\UserResponse;
use App\Components\Captcha\Drivers\Recaptcha\Verifier;
use App\Components\Captcha\Testing\Driver;
use Closure;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Builds a recaptcha driver for testing purposes.
 */
class DriverBuilder
{
    /**
     * Response generator for the recaptcha verifier.
     *
     * @var null|Closure(UserResponse): void
     */
    protected ?Closure $recaptchaResponseGenerator = null;

    /**
     * Constructs a new driver builder.
     */
    public function __construct(
        protected ?string $siteKey = null,
        protected ?string $secretKey = null,
        protected float $minimumScore = 0.5,
        protected bool $ready = true,
    ) {}

    /**
     * Generates and sets a random site key.
     *
     * @return $this
     */
    public function randomSiteKey(): static
    {
        $this->siteKey = Str::random(40);

        return $this;
    }

    /**
     * Generates and sets a random secret key.
     *
     * @return $this
     */
    public function randomSecretKey(): static
    {
        $this->secretKey = Str::random(40);

        return $this;
    }

    /**
     * Sets the site key.
     *
     * @return $this
     */
    public function withSiteKey(string $siteKey): static
    {
        $this->siteKey = $siteKey;

        return $this;
    }

    /**
     * Sets the secret key.
     *
     * @return $this
     */
    public function withSecretKey(string $secretKey): static
    {
        $this->secretKey = $secretKey;

        return $this;
    }

    /**
     * Sets the minimum score.
     *
     * @return $this
     */
    public function withMinimumScore(float $minimumScore): static
    {
        $this->minimumScore = $minimumScore;

        return $this;
    }

    /**
     * Sets the driver to be ready.
     *
     * @return $this
     */
    public function ready($ready = true): static
    {
        $this->ready = $ready;

        return $this;
    }

    /**
     * Sets the recaptcha response generator.
     *
     * @return $this
     */
    public function withRecaptchaResponseGenerator(callable $callback): static
    {
        $this->recaptchaResponseGenerator = $callback;

        return $this;
    }

    /**
     * Sets the recaptcha response generator to return a successful response for a matching response code.
     *
     * @param  string  $responseCode  Expected response code.
     * @param  array  $errorCodes  Error codes to return.
     * @return $this
     */
    public function expectsResponseCode(string $responseCode, array $errorCodes = []): static
    {
        $this->recaptchaResponseGenerator = function (UserResponse $userResponse) use ($responseCode, $errorCodes) {
            return $this->generateResponse([
                'success' => $responseCode === $userResponse->response,
                'error-codes' => $errorCodes,
            ]);
        };

        return $this;
    }

    /**
     * Sets the recaptcha response generator to return a successful response with score.
     *
     * @return $this
     */
    public function withValidResponse(float $score = 0.9): static
    {
        $this->recaptchaResponseGenerator = function () use ($score) {
            return $this->generateResponse(['success' => true, 'score' => $score]);
        };

        return $this;
    }

    /**
     * Sets the recaptcha response generator to return an invalid response.
     *
     * @return $this
     */
    public function withInvalidResponse(array $errorCodes = [], float $score = 0.1): static
    {
        $this->recaptchaResponseGenerator = function () use ($errorCodes, $score) {
            return $this->generateResponse([
                'success' => false,
                'error-codes' => $errorCodes,
                'score' => $score,
            ]);
        };

        return $this;
    }

    /**
     * Sets the recaptcha response generator to return a successful response with a random error code.
     *
     * @return $this
     */
    public function withRandomErrorCodes(int $count = 1, float $score = 0.1): static
    {
        $errorCodes = Arr::random(array_keys(Verifier::getErrorMappings()), $count);

        return $this->withInvalidResponse($errorCodes, $score);
    }

    /**
     * Gets the site key.
     */
    public function getSiteKey(): string
    {
        if (! $this->siteKey) {
            $this->randomSiteKey();
        }

        return $this->siteKey;
    }

    /**
     * Gets the secret key.
     */
    public function getSecretKey(): string
    {
        if (! $this->secretKey) {
            $this->randomSecretKey();
        }

        return $this->secretKey;
    }

    /**
     * Gets the minimum score.
     */
    public function getMinimumScore(): float
    {
        return $this->minimumScore;
    }

    /**
     * Checks if the driver is ready.
     */
    public function isReady(): bool
    {
        return $this->ready;
    }

    /**
     * Gets the recaptcha response generator.
     */
    public function getRecaptchaResponseGenerator(): ?callable
    {
        return $this->recaptchaResponseGenerator;
    }

    /**
     * Builds the recaptcha driver.
     */
    public function build(): DriverContract
    {
        return new Driver(
            $this->buildPresenter(),
            $this->buildVerifier(),
            $this->isReady()
        );
    }

    /**
     * Builds the recaptcha presenter.
     *
     * @return Presenter
     */
    public function buildPresenter()
    {
        return new Presenter($this->getSiteKey());
    }

    /**
     * Builds the recaptcha verifier.
     *
     * @return TestingVerifier
     */
    public function buildVerifier()
    {
        return (new TestingVerifier($this->getSiteKey(), $this->getSecretKey(), $this->getMinimumScore()))->useRequestCallback($this->getRecaptchaResponseGenerator());
    }

    /**
     * Generates a response from the recaptcha verifier.
     *
     * @param  array  $data  Request data
     */
    public function generateResponse(array $data): Response
    {
        return new Response(Factory::response($data)->wait());
    }

    /**
     * Runs new driver builder through callback and returns driver.
     */
    public static function create(?callable $callback = null): DriverContract
    {
        $driver = $callback ? tap(new static, $callback) : new static;

        return $driver->build();
    }
}
