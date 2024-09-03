<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * @property ?string $name
 * @property ?string $email
 * @property-read string $display_name
 * @property-read ?User $user
 * @property-read string $avatar_url
 * @property ?\DateTimeInterface $email_verified_at
 * @property-read Comment $comment
 *
 * @method static \Database\Factories\PersonFactory factory($count = null, $state = [])
 */
class Person extends Model
{
    use HasFactory;
    use MustVerifyEmail;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'user_id',
    ];

    public static function guest(string $name, string $email): static
    {
        return static::firstOrCreate(['name' => $name, 'email' => $email]);
    }

    public static function registered($user): static
    {
        $userId = $user instanceof User ? $user->getKey() : $user;

        return static::firstOrCreate(['user_id' => $userId]);
    }

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
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return 'person_'.$this->getKeyName();
    }

    /**
     * Gets the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return $this->user ? $this->user->hasVerifiedEmail() : ! is_null($this->email_verified_at);
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendMailable(Mailable $mailable)
    {
        Mail::to($this->getEmailForVerification())
            ->send($mailable);
    }

    /**
     * Gets the persons name
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->user ? $this->user->name : $value,
            // Restricts name being changed if user is set
            set: fn ($value) => $this->user ? $this->name : $value
        );
    }

    /**
     * Gets the persons email
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->user ? $this->user->email : $value,
            // Restricts email being changed if user is set
            set: fn ($value) => $this->user ? $this->email : $value
        );
    }

    /**
     * Gets the displayable name of the person
     */
    protected function displayName(): Attribute
    {
        // TODO: Make this a trait.
        return Attribute::get(fn () => $this->name ?? Str::before($this->email, '@'));
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn () => sprintf('https://gravatar.com/avatar/%s', Str::of($this->email)->trim()->lower()->hash('sha256')))->shouldCache();
    }
}
