<?php

namespace App\Components\Ntfy\Services;

use Ntfy\Exception\NtfyException;
use Ntfy\Message;

/**
 * Fluent builder for composing ntfy Message instances.
 */
class MessageBuilder
{
    protected Message $message;

    /**
     * Create a new builder instance.
     */
    public function __construct()
    {
        $this->message = new Message;
    }

    /**
     * Static constructor for quick chaining.
     */
    public static function make(): self
    {
        return new self;
    }

    /**
     * Set the required topic for the message.
     */
    public function topic(string $topic): self
    {
        $this->message->topic($topic);

        return $this;
    }

    /**
     * Set the message title.
     */
    public function title(string $title): self
    {
        $this->message->title($title);

        return $this;
    }

    /**
     * Set the plaintext body.
     */
    public function body(string $body): self
    {
        $this->message->body($body);

        return $this;
    }

    /**
     * Set a markdown-formatted body.
     */
    public function markdown(string $markdown): self
    {
        $this->message->markdownBody($markdown);

        return $this;
    }

    /**
     * Set the message priority (1-5).
     */
    public function priority(int $priority): self
    {
        $this->message->priority($priority);

        return $this;
    }

    /**
     * Attach tags (emojis or strings).
     */
    public function tags(array $tags): self
    {
        $this->message->tags($tags);

        return $this;
    }

    /**
     * Schedule delivery with a delay string (e.g. "10m", "1h").
     */
    public function schedule(string $delay): self
    {
        $this->message->schedule($delay);

        return $this;
    }

    /**
     * Set a click action URL.
     */
    public function click(string $url): self
    {
        $this->message->clickAction($url);

        return $this;
    }

    /**
     * Set an icon URL for the notification.
     */
    public function icon(string $url): self
    {
        $this->message->icon($url);

        return $this;
    }

    /**
     * Set an email address to forward the notification.
     */
    public function email(string $email): self
    {
        $this->message->email($email);

        return $this;
    }

    /**
     * Attach a file via URL with an optional filename.
     */
    public function attach(string $url, string $name = ''): self
    {
        $this->message->attachURL($url, $name);

        return $this;
    }

    /**
     * Disable caching for this message.
     */
    public function disableCaching(): self
    {
        $this->message->disableCaching();

        return $this;
    }

    /**
     * Disable Firebase for this message.
     */
    public function disableFirebase(): self
    {
        $this->message->disableFirebase();

        return $this;
    }

    /**
     * Finalize and return the Message instance.
     *
     * @throws NtfyException if required fields are missing when retrieved
     */
    public function build(): Message
    {
        return $this->message;
    }

    /**
     * Reset the builder to a new Message instance.
     */
    public function reset(): self
    {
        $this->message = new Message;

        return $this;
    }
}
