<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @property string $uuid
 * @property string $content
 * @property string $summary
 * @property-read bool $summary_auto
 * @property-read Article $article
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 *
 * @method static \Database\Factories\RevisionFactory factory($count = null, $state = [])
 */
class Revision extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'content',
        'summary',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'summary_auto',
    ];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * Gets the Article this belongs to.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Gets the parent revision of this revision
     */
    public function parentRevision(): BelongsTo
    {
        return $this->belongsTo(static::class);
    }

    /**
     * Get and set the summary
     */
    protected function summary(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_null($value) ? static::generateSummaryFrom($this->content) : $value,
            set: fn ($value) => is_string($value) ? Str::stripTags($value) : $value,
        );
    }

    /**
     * Checks if summary is auto generated
     *
     * @return bool
     */
    public function isSummaryAutoGenerated()
    {
        return is_null($this->attributes['summary']);
    }

    /**
     * Checks if summary is auto generated
     */
    public function summaryAuto(): Attribute
    {
        return Attribute::get(fn () => $this->isSummaryAutoGenerated());
    }

    /**
     * Generates summary from description
     *
     * @return string
     */
    public static function generateSummaryFrom(string $description)
    {
        $sentences = Str::sentences(Str::stripTags($description), 3);

        return Arr::join($sentences, ' ');
    }
}
