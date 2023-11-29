<?php

namespace App\Components\Security\Issues;

use Carbon\Carbon;
use App\Components\Security\Enums\Severity;

class ComposerAuditAbandonedAdvisory extends Issue {
    private Carbon $dateTime;

    public function __construct(
        public readonly string $package,
        public readonly ?string $replacement,
        ?Carbon $dateTime = null,
        protected readonly ?Severity $severity = null
    )
    {
        $this->dateTime = $dateTime ?? Carbon::now();
    }

    public function getSeverity(): Severity
    {
        return $this->severity ?? Severity::Medium;
    }

    public function getDateTime(): Carbon
    {
        return $this->dateTime;
    }

    public function getIdentifier(): string
    {
        return !is_null($this->replacement) ? implode('|', [$this->package, $this->replacement]) : $this->package;
    }

    public function getMessage(): string
    {
        return __('The ":package" package is abandoned and should not be used.', ['package' => $this->package]);
    }

    public function getContext(): array
    {
        return [
            'package' => $this->package,
            'replacement' => $this->replacement
        ];
    }
}
