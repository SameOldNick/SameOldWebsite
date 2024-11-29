<?php

namespace App\Components\Websockets\Notifiers;

use App\Components\Websockets\Notifications\Process\ProcessBegin;
use App\Components\Websockets\Notifications\Process\ProcessComplete;
use App\Components\Websockets\Notifications\Process\ProcessOutput;
use DateTimeInterface;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Support\Str;
use Ramsey\Uuid\UuidInterface;

/**
 * Broadcasts events related to a process.
 */
class ProcessNotifier extends AbstractNotifier
{
    /**
     * Default maximum length of messages
     */
    const DEFAULT_MAX_LENGTH = 7000;

    /**
     * The maximum length for messages.
     *
     * @var integer
     */
    public readonly int $maxLength;

    /**
     * Initializes ProcessNotifier instance
     *
     * @param  UuidInterface  $uuid  Process UUID
     * @param  object  $notifiable  Who to route notifications to
     * @param int $maxLength Maximum length of messages
     */
    public function __construct(
        public readonly UuidInterface $uuid,
        public readonly object $notifiable,
        ?int $maxLength,
    ) {
        $this->maxLength = $maxLength ?? static::DEFAULT_MAX_LENGTH;
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
        try {
            $this->notify($this->notifiable, new ProcessOutput($this->uuid, $message, $newline, $dateTime));
        } catch (BroadcastException $ex) {
            if ($ex->getMessage() === 'Pusher error: Payload too large..') {
                // Split message into chunks
                // The max size is 10KB
                $this->outputAsChunks($message, $newline, $dateTime);
            } else {
                throw $ex;
            }
        }
    }

    /**
     * Outputs message in chunks
     *
     * @param string $message
     * @param boolean $newline
     * @param DateTimeInterface|null $dateTime
     * @return void
     */
    public function outputAsChunks(string $message, bool $newline, ?DateTimeInterface $dateTime = null)
    {
        $chunks = Str::splitIntoChunks($message, $this->maxLength);
        $numChunks = count($chunks);

        for ($i = 0; $i < $numChunks; $i++) {
            $this->notify(
                $this->notifiable,
                new ProcessOutput(
                    $this->uuid,
                    $chunks[$i],
                    // Include newline in last chunk (if specified)
                    $newline && $i + 1 === $numChunks,
                    $dateTime
                )
            );
        }
    }
}
