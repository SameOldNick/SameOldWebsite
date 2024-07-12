<?php

namespace App\Models;

use App\Mail\CommentVerification;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * @property ?string $name
 * @property string $email
 * @property-read string $avatar_url
 * @property ?\DateTimeInterface $email_verified_at
 * @property-read Comment $comment
 *
 * @method static \Database\Factories\CommenterFactory factory($count = null, $state = [])
 */
class Commenter extends Model
{
    use HasFactory;
    use MustVerifyEmail {
        sendEmailVerificationNotification as traitSendEmailVerificationNotification;
    }
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Get the associated comment.
     */
    public function comment(): HasOne
    {
        return $this->hasOne(Comment::class);
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification(string $link)
    {
        Mail::to($this->getEmailForVerification())
            ->send((new CommentVerification)->with(['link' => $link]));
    }

    /**
     * Gets if email has been verified.
     */
    public function isVerified(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Gets the displayable name of the commenter
     */
    protected function displayName(): Attribute
    {
        // TODO: Make this a trait.
        return Attribute::get(fn () => $this->name ?? Str::before($this->email, '@'));
    }

    protected function avatarUrl(): Attribute {
        return Attribute::get(fn () => sprintf('https://gravatar.com/avatar/%s', Str::of($this->email)->trim()->lower()->hash('sha256')))->shouldCache();
    }
}
