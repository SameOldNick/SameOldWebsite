<?php

namespace App\Components\Security\Issues;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use App\Components\Security\Enums\Severity;

abstract class Issue implements Arrayable {
    /**
     * Gets the issue severity.
     *
     * @return Severity
     */
    abstract public function getSeverity(): Severity;

    /**
     * Gets the unique identifier for the issue.
     *
     * @return string
     */
    abstract public function getIdentifier(): string;

    /**
     * Gets the date/time for the issue.
     *
     * @return Carbon
     */
    abstract public function getDateTime(): Carbon;

    /**
     * Gets the message for the issue.
     *
     * @return string
     */
    abstract public function getMessage(): string;

    /**
     * Gets the full unique identifier.
     *
     * @return string
     */
    public function getFullIdentifier(): string
    {
        return implode('|', [static::class, $this->getIdentifier()]);
    }

    /**
     * Gets additional context for the issue.
     *
     * @return array
     */
    public function getContext(): array
    {
        return [];
    }

    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray()
    {
        return [
            'id' => $this->getFullIdentifier(),
            'severity' => $this->getSeverity()->value,
            'datetime' => $this->getDateTime(),
            'message' => $this->getMessage(),
            'context' => $this->getContext()
        ];
    }
}
