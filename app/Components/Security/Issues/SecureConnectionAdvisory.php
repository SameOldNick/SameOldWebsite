<?php

namespace App\Components\Security\Issues;

use Carbon\Carbon;
use Exception;
use App\Components\Security\Enums\Severity;

class SecureConnectionAdvisory extends Issue {
    private Carbon $dateTime;

    public function __construct(
        protected readonly string $url,
        protected readonly string $message,
        protected readonly Exception $exception,
        ?Carbon $dateTime = null,
        protected readonly ?Severity $severity = null
    )
    {
        $this->dateTime = $dateTime ?? Carbon::now();
    }

    public function getSeverity(): Severity
    {
        return $this->severity ?? Severity::High;
    }

    public function getDateTime(): Carbon
    {
        return $this->dateTime;
    }

    public function getIdentifier(): string
    {
        return $this->url;
    }

    public function getMessage(): string
    {
        return __('The URL ":url" is not secure.', ['url' => $this->url]);
    }

    public function getContext(): array
    {
        return [
            'url' => $this->url,
            'message' => $this->message
        ];
    }
}
