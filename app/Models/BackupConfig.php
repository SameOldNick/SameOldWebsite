<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $key
 * @property string $value
 */
class BackupConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string|null
     */
    protected $table = 'backup_config';

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
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Updates or creates a BackupConfig model with key
     *
     * @param  callable(array): array  $callback  Existing items (if any) are passed and returned array is saved.
     */
    public static function updateOrCreateArrayValue(string $key, callable $callback): self
    {
        // Retrieve the existing configuration or create a new instance
        $backupConfig = self::firstOrNew(['key' => $key]);

        // Explode the value into an array if it exists; otherwise, use an empty array
        // Ensure 'value' is not empty as explode will change '' into [''] (not [])
        $items = $backupConfig->exists && $backupConfig->value ? explode(';', $backupConfig->value) : [];

        // Apply the callback to modify the array
        $updatedItems = $callback($items);

        // Update or create the configuration with the modified value
        $backupConfig->value = implode(';', $updatedItems);
        $backupConfig->save();

        return $backupConfig;
    }
}
