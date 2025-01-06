<?php

namespace App\Components\Moderator\Exceptions;

use App\Models\ContactMessageFlag;

/**
 * @extends FlagException<ContactMessageFlag>
 */
class ContactFlagException extends FlagException
{
    /**
     * Any extra data about flag.
     */
    protected array $extra;

    /**
     * Initializes FlagCommentException
     */
    public function __construct(string $message, array $extra = [])
    {
        parent::__construct($message);

        $this->extra = $extra;
    }

    /**
     * Gets extra data for flag.
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * {@inheritDoc}
     */
    public function transformToFlag()
    {
        return new ContactMessageFlag([
            'reason' => $this->getMessage(),
            'extra' => $this->getExtra(),
        ]);
    }
}
