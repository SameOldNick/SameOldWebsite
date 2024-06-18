<?php

namespace App\Models;

use App\Models\Collections\PrivateChannelCollection;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\UuidInterface;

/**
 * @property string $uuid
 * @property string $channel
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property ?DateTimeInterface $expires_at
 */
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function notifiable()
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
    public function close()
    {
        $this->delete();

        return $this;
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array<int, \Illuminate\Database\Eloquent\Model>  $models
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function newCollection(array $models = []): Collection
    {
        return new PrivateChannelCollection($models);
    }

    public static function open(UuidInterface $uuid, object $notifiable, ?DateTimeInterface $expiresAt = null): static
    {
        $channel = new PrivateChannel([
            'uuid' => (string) $uuid,
            'expires_at' => $expiresAt ?? now()->addHours(3),
        ]);

        $channel->notifiable()->associate($notifiable);
        $channel->save();

        return $channel;
    }
}
