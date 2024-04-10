<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentPosted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Comment $comment
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $displayName = $this->comment->post->user->getDisplayName();
        $content = $this->comment->comment;

        return (new MailMessage)
            ->greeting('Hello!')
            ->line(sprintf('A new comment has been posted on the blog article, "%s".', $this->comment->article->title))
            ->line(sprintf('%s said: "%s"', $displayName, $content))
            ->action('View Comment', route('blog.comment.show', ['article' => $this->comment->article, 'comment' => $this->comment]))
            ->line('Thank you for being an active member!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
