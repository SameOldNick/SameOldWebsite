<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $name
 * @property string $path
 * @property string $disk
 * @property bool $is_public
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 * @property-read ?Model $fileable
 * @property-read array $pathInfo
 * @property-read bool $fileExists
 *
 * @method static \Database\Factories\FileFactory factory($count = null, $state = [])
 */
final class File extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

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
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'path',
        'name',
        'is_public',
        'disk',
    ];

    /**
     * The attributes that should be visible in serialization.
     *
     * @var list<string>
     */
    protected $visible = [
        'id',
        'name',
        'url',
        'meta',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'url',
        'meta',
    ];

    /**
     * Whether to include URL when serialized.
     * Redundant when is_public is true.
     *
     * @var bool
     */
    protected $withUrl = false;

    /**
     * Gets the parent fileable model (ProductImage, Download, or Release)
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Gets the user who uploaded this file.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sets model to include URL in serialization.
     *
     * @return $this
     */
    public function withUrl(bool $enabled = true): static
    {
        $this->withUrl = $enabled;

        return $this;
    }

    /**
     * Deletes file (if removeFileOnDelete in morph model is true) before deleting record from database.
     *
     * @return bool|null
     */
    public function delete()
    {
        if (! is_null($this->fileable)) {
            if (method_exists($this->fileable, 'removeFileOnDelete') && $this->fileable->removeFileOnDelete()) {
                $this->removeFile();
            }

            $this->fileable->delete();
        }

        return parent::delete();
    }

    /**
     * Gets the storage disk the file is from.
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function getStorageDisk()
    {
        return Storage::disk($this->disk);
    }

    /**
     * Removes file from storage.
     *
     * @return bool
     */
    public function removeFile()
    {
        return $this->getStorageDisk()->delete($this->path);
    }

    /**
     * Gets whether the file exists or not
     */
    protected function fileExists(): Attribute
    {
        return Attribute::get(fn ($value, $attributes) => $this->getStorageDisk()->exists($attributes['path']));
    }

    /**
     * Get and set the filename
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => is_null($value) ? Str::of($attributes['path'])->basename() : $value,
            set: fn ($value) => $value,
        );
    }

    /**
     * Get the URL for the file
     */
    protected function url(): Attribute
    {
        return Attribute::get(fn ($value, $attributes) => $attributes['is_public'] || $this->withUrl ? $this->createPublicUrl() : null);
    }

    /**
     * Get the meta-data for the file
     */
    protected function meta(): Attribute
    {
        $defaults = [];

        return Attribute::get(fn ($value, $attributes) => $this->fileExists ? [
            'size' => $this->getStorageDisk()->size($attributes['path']),
            'last_modified' => Carbon::parse($this->getStorageDisk()->lastModified($attributes['path'])),
            'mime_type' => $this->getStorageDisk()->mimeType($attributes['path']),
        ] : $defaults);
    }

    /**
     * Gets the pathinfo for the file.
     */
    protected function pathInfo(): Attribute
    {
        return Attribute::get(fn ($value, $attributes) => pathinfo(Storage::path($attributes['path'])));
    }

    /**
     * Creates public URL to access this file.
     * Note: The 'is_public' attribute needs to be true in order for this URL to work.
     *
     * @param  bool  $absolute  If true, absolute URL is returned. (default: true)
     * @param  bool  $withExt  If true, includes file extension in URL. (default: true)
     * @return string
     */
    public function createPublicUrl(bool $absolute = true, bool $withExt = true)
    {
        $url = URL::route('file', ['file' => $this], $absolute);

        if (! $withExt || ! ($ext = $this->pathInfo['extension'])) {
            return $url;
        }

        return sprintf('%s.%s', $url, $ext);
    }

    /**
     * Creates temporary signed URL to this file
     *
     * @param  int  $minutes  Minutes until URL expires (default: 30)
     * @param  bool  $absolute  If true, absolute URL is returned. (default: true)
     * @return string
     */
    public function createPrivateUrl(int $minutes = 30, bool $absolute = true)
    {
        // Can't insert file extension because it will ruin integrity of signature
        return URL::temporarySignedRoute('file', $minutes * 60, ['file' => $this], $absolute);
    }

    /**
     * Creates File model from file path
     *
     * @param  string  $path  Path of file
     * @param  string|null  $name  Filename. If null, filename is generated from path. (default: null)
     * @param  bool  $public  If file is public (default: false)
     * @param  string  $disk  Name of the disk (default: null)
     * @return static
     */
    public static function createFromFilePath(string $path, ?string $name = null, bool $public = false, ?string $disk = null): self
    {
        return new self([
            'path' => $path,
            'name' => $name,
            'is_public' => $public,
            'disk' => $disk,
        ]);
    }
}
