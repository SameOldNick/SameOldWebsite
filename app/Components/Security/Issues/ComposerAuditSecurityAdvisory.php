<?php

namespace App\Components\Security\Issues;

use Carbon\Carbon;
use App\Components\Security\Enums\Severity;

class ComposerAuditSecurityAdvisory extends Issue {
    private Carbon $dateTime;

    public function __construct(
        public readonly string $package,
        public readonly array $advisories,
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
        $advisoryIds = array_map(fn ($advisory) => $this->getAdvisoryId($advisory), $this->advisories);

        return implode('|', [$this->package, ...$advisoryIds]);
    }

    private function getAdvisoryId(array $advisory): string
    {
        return $advisory['advisoryId'];
    }

    public function getMessage(): string
    {
        return __('A security advisory for the ":package" package was detected.', ['package' => $this->package]);
    }

    public function getContext(): array
    {
        return [
            'package' => $this->package,
            'advisories' => $this->advisories
        ];
    }
}
