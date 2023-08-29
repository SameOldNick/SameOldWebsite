<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * @var array
     */
    protected $with = [
        'states'
    ];

    /**
     * The codes of country to include in Javascript (for caching)
     *
     * @var array
     */
    protected static $includeInJs = [
        'CAN',
        'USA',
    ];

    /**
     * Gets the users with this country.
     *
     * @return mixed
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Gets the states for this country.
     *
     * @return mixed
     */
    public function states()
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

    /**
     * Gets countries to transfer to JavaScript.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getTransferToJs()
    {
        return static::whereIn('code', static::$includeInJs)->get()->mapWithKeys(fn ($country) => [
            $country->code => [
                'country' => $country->country,
                'states' => $country->states,
            ],
        ]);
    }
}
