<?php

namespace App\Components\Websockets\Notifiers;

use App\Components\Websockets\Notifications\Process\ProcessBegin;
use App\Components\Websockets\Notifications\Process\ProcessComplete;
use App\Components\Websockets\Notifications\Process\ProcessOutput;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Broadcasts events related to a process.
 */
class ProcessNotifier extends AbstractNotifier
{
    /**
     * Initializes ProcessNotifier instance
     *
     * @param  UuidInterface  $uuid  Process UUID
     * @param  object  $notifiable  Who to route notifications to
     */
    public function __construct(
        public readonly UuidInterface $uuid,
        public readonly object $notifiable,
    ) {
    }

    /**
     * Sends notification that process was started.
     *
     * @return void
     */
    public function begin(?DateTimeInterface $dateTime = null)
    {
        $this->notify($this->notifiable, new ProcessBegin($this->uuid, $dateTime));
    }

    /**
     * Sends notification that process completed.
     *
     * @param  int  $errorCode  Process error code
     * @return void
     */
    public function complete(int $errorCode, ?DateTimeInterface $dateTime = null)
    {
        $this->notify($this->notifiable, new ProcessComplete($this->uuid, $errorCode, $dateTime));
    }

    /**
     * Sends notification with output from process.
     *
     * @return void
     */
    public function output(string $message, bool $newline, ?DateTimeInterface $dateTime = null)
    {
        $this->notify($this->notifiable, new ProcessOutput($this->uuid, $message, $newline, $dateTime));
    }
}
