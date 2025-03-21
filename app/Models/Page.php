<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $page
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PageMetaData> $metaData
 */
class Page extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['page'];

    /**
     * Gets meta data for this page.
     */
    public function metaData(): HasMany
    {
        return $this->hasMany(PageMetaData::class);
    }
}
