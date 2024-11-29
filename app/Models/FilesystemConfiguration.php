<?php

namespace App\Models;

use App\Components\Backup\Contracts\FilesystemConfiguration as FilesystemConfigurationContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $disk_type
 * @property string $configurable_type
 * @property int $configurable_id
 * @property string $driver_name
 * @property-read ?Model $configurable
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @method static \Database\Factories\FilesystemConfigurationFactory factory($count = null, $state = [])
 */
class FilesystemConfiguration extends Model implements FilesystemConfigurationContract
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'disk_type',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'configurable',
    ];

    public function configurable()
    {
        return $this->morphTo();
    }

    /**
     * {@inheritDoc}
     */
    public function getFilesystemConfig(): array
    {
        $options =
            $this->configurable instanceof FilesystemConfigurationContract ?
            $this->configurable->getFilesystemConfig() :
            [];

        return [
            'name' => $this->name,
            'driver' => $this->disk_type,
            ...$options,
        ];
    }

    /**
     * Gets the driver name
     * Used to pull the configuration from the filesystem manager
     */
    protected function driverName(): Attribute
    {
        return Attribute::get(fn ($value, $attributes = []) => "dynamic-{$attributes['name']}");
    }

    public function toArray()
    {
        return [
            $this->getKeyName() => $this->getKey(),
            'name' => $this->name,
            'type' => $this->disk_type,
            ...$this->configurable->toArray(),
        ];
    }
}
