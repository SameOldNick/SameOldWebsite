<?php

namespace App\Components\Backup\SpatieBackup;

use Spatie\Backup\Support\Data;

class NotificationNtfyConfig extends Data
{
    /**
     * Constructor
     */
    protected function __construct(
        public string $topic,
    ) {}

    /** @param array<mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            topic: $data['topic'],
        );
    }
}
