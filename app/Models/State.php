<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $country_code
 * @property string $code
 * @property string $state
 * @property-read Country $country
 */
class State extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['code', 'state'];

    /**
     * The attributes that should be visible in serialization.
     *
     * @var list<string>
     */
    protected $visible = ['code', 'state'];

    /**
     * Gets the country for this state
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Gets state as readable string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->state;
    }
}
