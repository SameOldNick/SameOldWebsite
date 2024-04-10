<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $state
 * @property-read Country $country
 */
class State extends Model
{
    use HasFactory;

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
     *
     * @return mixed
     */
    public function country()
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
