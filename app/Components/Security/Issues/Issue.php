<?php

namespace App\Components\Security\Issues;

use App\Components\Security\Enums\Severity;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

abstract class Issue implements Arrayable
{
    /**
     * Gets the issue severity.
     */
    abstract public function getSeverity(): Severity;

    /**
     * Gets the unique identifier for the issue.
     */
    abstract public function getIdentifier(): string;

    /**
     * Gets the date/time for the issue.
     */
    abstract public function getDateTime(): Carbon;

    /**
     * Gets the message for the issue.
     */
    abstract public function getMessage(): string;

    /**
     * Gets the full unique identifier.
     */
    public function getFullIdentifier(): string
    {
        return implode('|', [static::class, $this->getIdentifier()]);
    }

    /**
     * Gets additional context for the issue.
     */
    public function getContext(): array
    {
        return [];
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->getFullIdentifier(),
            'severity' => $this->getSeverity()->value,
            'datetime' => $this->getDateTime()->toIso8601String(),
            'message' => $this->getMessage(),
            'context' => $this->getContext(),
        ];
    }
}
