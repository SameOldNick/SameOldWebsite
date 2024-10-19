<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $code
 * @property-read \Illuminate\Database\Eloquent\Collection<int, State> $states
 */
class Country extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'code';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The relations to eager load on every query.
     *
     * @var list<string>
     */
    protected $with = [
        'states',
    ];

    /**
     * Gets the users with this country.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Gets the states for this country.
     */
    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    /**
     * Gets country as readable string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->country;
    }

    /**
     * Gets countries sorted by country name.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function sortedByCountry()
    {
        return static::orderBy('country', 'asc')->get();
    }

    /**
     * Gets countries sorted by country code.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function sortedByCode()
    {
        return static::orderBy('code', 'asc')->get();
    }
}
