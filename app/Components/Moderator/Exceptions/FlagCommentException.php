<?php

namespace App\Components\Moderator\Exceptions;

use Exception;

class FlagCommentException extends Exception {
    /**
     * Proposed new comment
     *
     * @var string|null
     */
    protected ?string $proposed;

    /**
     * Any extra data about flag.
     *
     * @var array
     */
    protected array $extra;

    /**
     * Initializes FlagCommentException
     *
     * @param string $message
     * @param string|null $proposed
     * @param array $extra
     */
    public function __construct(string $message, ?string $proposed = null, array $extra = [])
    {
        parent::__construct($message);

        $this->proposed = $proposed;
        $this->extra = $extra;
    }

    /**
     * Gets proposed comment.
     *
     * @return string|null
     */
    public function getProposed(): ?string {
        return $this->proposed;
    }

    /**
     * Gets extra data for flag.
     *
     * @return array
     */
    public function getExtra(): array {
        return $this->extra;
    }
}
