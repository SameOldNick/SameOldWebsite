<?php

namespace App\Components\Security\Watchdogs;

use App\Components\Security\Enums\Severity;
use App\Components\Security\Issues\SecureConnectionAdvisory;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Throwable;

final class HttpSecureWatchdog implements WatchdogDriver
{
    public function __construct(
        protected readonly array $config
    ) {}

    /**
     * Initializes the watchdog.
     */
    public function initialize(): void {}

    /**
     * Sniff for issues.
     *
     * @return array<\App\Components\Security\Issues\Issue>
     */
    public function sniff(): array
    {
        $issues = [];
        $url = $this->getSecureUrl();

        try {
            $response = Http::get($url);
        } catch (Throwable $ex) {
            if ($this->isSecureConnectionError($ex)) {
                $issue = $this->transformExceptionToIssue($ex);

                array_push($issues, $issue);
            }
        }

        return $issues;
    }

    /**
     * Cleans up with watchdog.
     *
     * @return void
     */
    public function cleanup() {}

    /**
     * Gets the secure URL to check.
     *
     * @return string
     */
    protected function getSecureUrl()
    {
        return $this->config['url'] ?? secure_url('');
    }

    /**
     * Gets valid curl error codes
     */
    protected function getCurlErrorCodes(): array
    {
        return [
            CURLE_SSL_CONNECT_ERROR,
            CURLE_SSL_CERTPROBLEM,
            CURLE_SSL_CIPHER,
            CURLE_SSL_PEER_CERTIFICATE,
            CURLE_SSL_CACERT,
        ];
    }

    /**
     * Gets curl error code from exception.
     *
     * @return int|null
     */
    protected function getCurlErrorCode(Throwable $ex)
    {
        $context = method_exists($ex, 'getHandlerContext') ? $ex->getHandlerContext() : [];

        return Arr::get($context, 'errno');
    }

    /**
     * Gets error message from curl error code.
     *
     * @return string|null
     */
    protected function getCurlErrorMessage(int $errorCode): string
    {
        return curl_strerror($errorCode);
    }

    /**
     * Checks if exception is SSL connection exception.
     *
     * @return bool
     */
    protected function isSecureConnectionError(Throwable $ex)
    {
        if (! ($ex instanceof RequestException || $ex instanceof ConnectException)) {
            return false;
        }

        $code = $this->getCurlErrorCode($ex);

        if (is_null($code)) {
            return false;
        }

        return in_array($code, $this->getCurlErrorCodes());
    }

    /**
     * Transforms exception to issue.
     *
     * @return SecureConnectionAdvisory
     */
    protected function transformExceptionToIssue(Throwable $ex)
    {
        $severity = Severity::from($this->config['level']);

        $code = $this->getCurlErrorCode($ex);
        $message = $this->getCurlErrorMessage($code);

        return new SecureConnectionAdvisory($this->getSecureUrl(), $message, $ex, null, $severity);
    }
}
