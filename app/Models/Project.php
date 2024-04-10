<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $project
 * @property string $description
 * @property string $url
 * @property \DateTimeInterface|null $created_at
 * @property \DateTimeInterface|null $updated_at
 * @property \DateTimeInterface|null $deleted_at
 */
class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'project',
        'description',
        'url',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var list<string>
     */
    protected $with = [
        'tags',
    ];

    /**
     * Gets the tags that belong to this project.
     *
     * @return mixed
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
