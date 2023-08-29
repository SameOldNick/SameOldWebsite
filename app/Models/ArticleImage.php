<?php

namespace App\Models;

use App\Traits\Models\Fileable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ArticleImage extends Model
{
    use HasFactory;
    use Fileable;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [];

    /**
     * The attributes that should be visible in serialization.
     *
     * @var array<string>
     */
    protected $visible = [
        'id',
        'description',
        'file',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['file'];

    /**
     * Gets the article this belongs to
     *
     * @return mixed
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Gets the alt-text for this image.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function alternativeText(): Attribute
    {
        return Attribute::get(fn ($value, $attributes) => $attributes['description'] ? Str::squish($attributes['description']) : $this->file->pathinfo['filename']);
    }
}
