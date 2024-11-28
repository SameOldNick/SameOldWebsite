<?php

namespace App\Models;

use App\Casts\Json;
use App\Traits\Models\HidesPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $page_id
 * @property string $key
 * @property mixed $value
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property-read Page $page
 */
class PageMetaData extends Model
{
    use HasFactory;
    use HidesPrimaryKey;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['key', 'value'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var list<string>
     */
    protected $hidden = ['page_id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
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
