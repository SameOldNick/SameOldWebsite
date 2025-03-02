<?php

namespace App\Components\Captcha\Drivers\Recaptcha;

use App\Components\Captcha\Contracts\Verifier as VerifierContract;
use App\Components\Captcha\Exceptions\VerificationException;
use Illuminate\Http\Client\PendingRequest as HttpClient;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * @mixes VerifierContract<UserResponse>
 */
class Verifier implements VerifierContract
{
    /**
     * Error codes and their descriptions.
     *
     * @var array<string, string>
     */
    public static array $errorCodes = [
        'missing-input-secret' => 'The secret parameter is missing.',
        'invalid-input-secret' => 'The secret parameter is invalid or malformed.',
        'missing-input-response' => 'The response parameter is missing.',
        'invalid-input-response' => 'The response parameter is invalid or malformed.',
        'bad-request' => 'The request is invalid or malformed.',
        'timeout-or-duplicate' => 'The response is no longer valid: either is too old or has been used previously.',
    ];

    /**
     * Constructs a new recaptcha verifier.
     */
    public function __construct(
        protected readonly string $siteKey,
        protected readonly string $secretKey,
        protected readonly float $minimumScore,
        protected readonly array $clientOptions = [],
    ) {}

    /**
     * {@inheritDoc}
     */
    public function verifyResponse($userResponse): void
    {
        if (! $userResponse instanceof UserResponse) {
            throw new \InvalidArgumentException('Invalid user response');
        }

        $response = $this->performRequest($userResponse);

        $this->handleResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function validateRule(string $attribute, mixed $value): void
    {
        if (! is_string($value)) {
            throw new VerificationException('The :attribute value must be a string.');
        }

        $this->verifyResponse($this->resolveUserResponse($attribute, $value));
    }

    /**
     * Resolves the user response.
     */
    public function resolveUserResponse(string $attribute, mixed $value): UserResponse
    {
        return new UserResponse($value, request()->ip());
    }

    /**
     * Performs the request to the reCAPTCHA API.
     */
    protected function performRequest(UserResponse $userResponse): Response
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $this->secretKey,
            'response' => $userResponse->response,
            'remoteip' => $userResponse->remoteIp,
        ];

        return $this->createHttpClient()->post($url, $data);
    }

    /**
     * Handles the response from the reCAPTCHA API.
     *
     * @return void
     *
     * @throws VerificationException If the response is invalid.
     */
    protected function handleResponse(Response $response)
    {
        if (! $response->successful()) {
            Log::error('Captcha verification failed. There maybe a firewall issue.', [
                'response' => $response->body(),
                'status' => $response->status(),
            ]);

            throw VerificationException::withReason($response->clientError() ? __('client error') : __('server error'));
        }

        $resultJson = $response->json();

        if (! (bool) Arr::get($resultJson, 'success', false)) {
            $errorCodes = Arr::get($resultJson, 'error-codes', []);

            $errorCode = Arr::first($errorCodes, fn ($errorCode) => Arr::has(static::$errorCodes, $errorCode));

            throw VerificationException::withReason($errorCode ? static::$errorCodes[$errorCode] : null);
        }

        if ((float) Arr::get($resultJson, 'score', 0) < $this->minimumScore) {
            throw VerificationException::withReason('score too low');
        }
    }

    /**
     * Creates a new HTTP client instance.
     */
    protected function createHttpClient(): HttpClient
    {
        return Http::asForm()->withOptions($this->clientOptions);
    }
}
