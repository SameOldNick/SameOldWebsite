<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Revision extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * Gets the Article this belongs to.
     *
     * @return mixed
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the compiled contents as HTML
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function compiled(): Attribute
    {
        return Attribute::get(fn ($value, $attributes) => Str::markdown($attributes['content']))->shouldCache();
    }
}
