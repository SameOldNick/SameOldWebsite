<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\Backup\Contracts\FilesystemConfiguration as FilesystemConfigurationContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $host
 * @property int $port
 * @property string $username
 * @property string $password
 * @property string|null $root
 * @property array|null $extra
 * @property-read ?FilesystemConfiguration $filesystemConfiguration
 *
 * @method static \Database\Factories\FilesystemConfigurationFTPFactory factory($count = null, $state = [])
 */
class FilesystemConfigurationFTP extends Model implements FilesystemConfigurationContract
{
    use HasFactory;

    /**
     * @inheritDoc
     */
    protected $table = 'filesystem_configuration_ftp';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'host',
        'port',
        'username',
        'password',
        'root',
        'extra'
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'password' => 'encrypted',
            'extra' => 'array',
        ];
    }

    /**
     * Gets base FilesystemConfiguration model.
     */
    public function filesystemConfiguration()
    {
        return $this->morphOne(FilesystemConfiguration::class, 'configurable');
    }

    /**
     * @inheritDoc
     */
    public function getFilesystemConfig(): array
    {
        $extra = $this->extra ?? [];

        return [
            'host' => $this->host,
            'port' => $this->port,
            'username' => $this->username,
            'password' => $this->password,
            'root' => $this->root,
            ...$extra,
        ];
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'host' => $this->host,
            'port' => $this->port,
            'auth_type' => 'password',
            'username' => $this->username,
        ];
    }
}
