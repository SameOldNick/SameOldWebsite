<?php

namespace App\Components\Security\Clerks\NotificationClerk;

use App\Components\Security\Issues\Issue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

final class IssueNotification extends Notification
{
    use Queueable;

    const DATABASE_TYPE_UUID = '513a8515-ae2a-47d9-9052-212b61f166b0';

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly array $data
    ) {
        //
    }

    /**
     * Gets the type to store in the 'type' column in the database table.
     *
     * @return string
     */
    public function databaseType()
    {
        return self::DATABASE_TYPE_UUID;
    }

    /**
     * Gets unique identifier for notification.
     *
     * @return string
     */
    public function getId()
    {
        return sha1($this->data['id']);
    }

    /**
     * Gets the severity
     *
     * @return string
     */
    public function getSeverity()
    {
        return $this->data['severity'];
    }

    /**
     * Gets the message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->data['message'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(sprintf('[%s] Issue Detected', Str::upper($this->getSeverity())))
            ->line('An issue has been detected!')
            ->line($this->getMessage())
            ->line('Ensure everything is working and up to date!')
            ->action('Visit Web App', url('/'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->getId(),
            'issue' => $this->data,
        ];
    }

    /**
     * Creates notification from Issue instance.
     *
     * @return static
     */
    public static function createFromIssue(Issue $issue)
    {
        return new self($issue->toArray());
    }
}
