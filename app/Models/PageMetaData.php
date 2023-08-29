<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageMetaData extends Model
{
    use HasFactory;
    use Concerns\HidesPrimaryKey;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['key', 'value'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['page_id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => Json::class,
    ];

    /**
     * Get the page that owns this meta data.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
