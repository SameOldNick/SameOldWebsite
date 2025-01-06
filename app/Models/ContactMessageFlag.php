<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $reason
 * @property array|null $extra
 * @property-read ContactMessage $contactMessage
 */
class ContactMessageFlag extends Model
{
    protected $fillable = [
        'reason',
        'extra'
    ];

    /**
     * Gets the original contact message.
     */
    public function contactMessage(): BelongsTo
    {
        return $this->belongsTo(ContactMessage::class);
    }

    /**
     * Mutator for extra data
     */
    protected function extra(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ! empty($value) ? $this->fromJson($value) : [],
            set: fn($value) => ! empty($value) ? $this->asJson($value) : null,
        );
    }
}
