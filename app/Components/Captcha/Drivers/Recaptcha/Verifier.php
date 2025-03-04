<?php

namespace App\Components\Captcha\Drivers\Recaptcha;

use App\Components\Captcha\Contracts\Verifier as VerifierContract;
use App\Components\Captcha\Exceptions\VerificationException;
use Illuminate\Http\Client\PendingRequest as HttpClient;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use function Safe\preg_match;

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
        'missing-input-secret' => __('The secret parameter is missing.'),
        'invalid-input-secret' => __('The secret parameter is invalid or malformed.'),
        'missing-input-response' => __('The response parameter is missing.'),
        'invalid-input-response' => __('The response parameter is invalid or malformed.'),
        'bad-request' => __('The request is invalid or malformed.'),
        'timeout-or-duplicate' => __('The response is no longer valid: either is too old or has been used previously.'),
    ];

    /**
     * Constructs a new recaptcha verifier.
     */
    public function __construct(
        protected readonly string $siteKey,
        protected readonly string $secretKey,
        protected readonly float $minimumScore,
        protected readonly array $clientOptions = [],
        protected readonly array $excludeIps = [],
    ) {}

    /**
     * {@inheritDoc}
     */
    public function verifyResponse($userResponse): void
    {
        if (! $userResponse instanceof UserResponse) {
            throw new \InvalidArgumentException('Invalid user response');
        }

        if ($this->isExcludedIp($userResponse->remoteIp)) {
            return;
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

    /**
     * Determines if the IP address is excluded from the verification.
     */
    protected function isExcludedIp(string $ip): bool
    {
        return count($this->excludeIps) > 0 ? Arr::first($this->excludeIps, fn ($excludeIp) => $this->matchesIp($ip, $excludeIp)) !== null : false;
    }

    /**
     * Matches the IP address with the expected value.
     */
    protected function matchesIp(string $ip, string $expected): bool
    {
        // Values that match any IP address.
        $allIps = [
            '*',
            '0.0.0.0/0',
            '::/0',
        ];

        if ($expected === $ip || Str::is($expected, $ip) || in_array($expected, $allIps, true)) {
            return true;
        }

        return match ($this->determineIpVersion($expected)) {
            4 => $this->matchesIpv4($ip, $expected),
            6 => $this->matchesIpv6($ip, $expected),
            default => false,
        };
    }

    /**
     * Determines the IP version of the expected value.
     */
    protected function determineIpVersion(string $ip): ?int
    {
        return match (true) {
            str_contains($ip, '.') => 4,
            str_contains($ip, ':') => 6,
            default => null,
        };
    }

    /**
     * Matches the IPv4 address with the expected value.
     */
    protected function matchesIpv4(string $ip, string $expected): bool
    {
        if (preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\/(\d\d?)$/', $expected, $matches)) {
            $excludeIp = $matches[1];
            $mask = 32 - (int) $matches[2];

            return (ip2long($ip) >> $mask) === (ip2long($excludeIp) >> $mask);
        }

        return false;
    }

    /**
     * Matches the IPv6 address with the expected value.
     */
    protected function matchesIpv6(string $ip, string $expected): bool
    {
        if (preg_match('/^([0-9a-fA-F:]+)\/(\d{1,3})$/', $expected, $matches)) {
            // Convert the IP addresses to binary.
            $excludeIp = inet_pton($matches[1]);
            $mask = (int) $matches[2];

            // Convert the IP addresses to binary.
            $ipBin = inet_pton($ip);
            $ipBin = unpack('A16', $ipBin)[1];
            $excludeIpBin = unpack('A16', $excludeIp)[1];

            // Pad the IP addresses to 16 bytes.
            $ipBin = str_pad($ipBin, 16, "\0", STR_PAD_RIGHT);
            $excludeIpBin = str_pad($excludeIpBin, 16, "\0", STR_PAD_RIGHT);

            for ($i = 0; $i < 16; $i++) {
                // Calculate the number of bits to compare.
                $maskBits = min(8, $mask);
                $mask = max(0, $mask - 8);

                // Compare the bits.
                if ((ord($ipBin[$i]) >> (8 - $maskBits)) !== (ord($excludeIpBin[$i]) >> (8 - $maskBits))) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
