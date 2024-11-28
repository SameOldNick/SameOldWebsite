<?php

namespace App\Models;

use App\Models\Collections\PrivateChannelCollection;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Ramsey\Uuid\UuidInterface;

/**
 * @property int $id
 * @property string $uuid
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property ?string $channel
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $expires_at
 */
#[CollectedBy(PrivateChannelCollection::class)]
class PrivateChannel extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'channel',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array
     */
    public function uniqueIds()
    {
        return ['uuid'];
    }

    /**
     * Get who the private channel belongs to.
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Checks if has expired.
     */
    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    /**
     * Closes the channel.
     *
     * @return $this
     */
    public function close(): static
    {
        $this->delete();

        return $this;
    }

    /**
     * Opens private channel
     *
     * @param  UuidInterface  $uuid  Channel UUID
     * @param  object  $notifiable  Who can access channel
     * @param  DateTimeInterface|null  $expiresAt  When channel expires
     */
    public static function open(UuidInterface $uuid, object $notifiable, ?DateTimeInterface $expiresAt = null): self
    {
        $channel = new self([
            'uuid' => (string) $uuid,
            'expires_at' => $expiresAt ?? now()->addHours(3),
        ]);

        $channel->notifiable()->associate($notifiable);
        $channel->save();

        return $channel;
    }
}
