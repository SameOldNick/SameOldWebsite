<?php

namespace App\Components\Moderator\Exceptions;

/**
 * @extends FlagException<array>
 */
class ContactFlagException extends FlagException
{
    /**
     * Initializes FlagCommentException
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * {@inheritDoc}
     */
    public function transformToFlag()
    {
        return [
            'reason' => $this->getMessage(),
        ];
    }
}
