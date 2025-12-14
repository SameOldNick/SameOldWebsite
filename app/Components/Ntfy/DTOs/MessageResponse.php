<?php

namespace App\Components\Ntfy\DTOs;

use Carbon\CarbonImmutable;

/**
 * Data Transfer Object for ntfy server message responses.
 */
class MessageResponse
{
    /**
     * Create a new MessageResponse instance.
     *
     * @param  array  $responseData  Raw response data from ntfy server
     */
    public function __construct(protected readonly array $responseData) {}

    /**
     * Get the unique message ID.
     */
    public function id(): ?string
    {
        return $this->responseData['id'] ?? null;
    }

    /**
     * Get the server timestamp of the message.
     */
    public function time(): ?int
    {
        return isset($this->responseData['time']) ? (int) $this->responseData['time'] : null;
    }

    /**
     * Get the message date and time as a DateTimeImmutable instance.
     */
    public function dateTime(): ?\DateTimeImmutable
    {
        if (! isset($this->responseData['time'])) {
            return null;
        }

        return CarbonImmutable::createFromTimestamp(
            (int) $this->responseData['time']
        );
    }

    /**
     * Get the topic the message was sent to.
     */
    public function topic(): ?string
    {
        return $this->responseData['topic'] ?? null;
    }

    /**
     * Get the message body content.
     */
    public function message(): ?string
    {
        return $this->responseData['message'] ?? null;

    }

    /**
     * Get the message title.
     */
    public function title(): ?string
    {
        return $this->responseData['title'] ?? null;
    }

    /**
     * Get the message priority level (1-5).
     */
    public function priority(): ?int
    {
        return $this->responseData['priority'] ?? null;
    }

    /**
     * Get the raw response data from the ntfy server.
     */
    public function getResponseData(): array
    {
        return $this->responseData;
    }
}
