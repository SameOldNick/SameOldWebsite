<?php

namespace App\Components\Moderator\Exceptions;

use App\Models\CommentFlag;

/**
 * @extends FlagException<CommentFlag>
 */
class FlagCommentException extends FlagException
{
    /**
     * Proposed new comment
     */
    protected ?string $proposed;

    /**
     * Any extra data about flag.
     */
    protected array $extra;

    /**
     * Initializes FlagCommentException
     */
    public function __construct(string $message, ?string $proposed = null, array $extra = [])
    {
        parent::__construct($message);

        $this->proposed = $proposed;
        $this->extra = $extra;
    }

    /**
     * Gets proposed comment.
     */
    public function getProposed(): ?string
    {
        return $this->proposed;
    }

    /**
     * Gets extra data for flag.
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @inheritDoc
     */
    public function transformToFlag() {
        return new CommentFlag([
            'reason' => $this->getMessage(),
            'proposed' => $this->getProposed(),
            'extra' => $this->getExtra(),
        ]);
    }
}
