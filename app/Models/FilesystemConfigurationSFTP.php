<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\Backup\Contracts\FilesystemConfiguration as FilesystemConfigurationContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $host
 * @property int $port
 * @property string $username
 * @property string|null $password
 * @property string|null $private_key
 * @property string|null $passphrase
 * @property array|null $extra
 * @property-read ?FilesystemConfiguration $filesystemConfiguration
 *
 * @method static \Database\Factories\FilesystemConfigurationSFTPFactory factory($count = null, $state = [])
 */
class FilesystemConfigurationSFTP extends Model implements FilesystemConfigurationContract
{
    use HasFactory;

    /**
     * @inheritDoc
     */
    protected $table = 'filesystem_configuration_sftp';

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
        'private_key',
        'passphrase',
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
            'private_key' => 'encrypted',
            'passphrase' => 'encrypted',
            'extra' => 'json',
        ];
    }

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
            'type' => 'sftp',
            'host' => $this->host,
            'port' => $this->port,
            'username' => $this->username,
            'password' => $this->password,
            'privateKey' => $this->private_key,
            'passphrase' => $this->passphrase,
            ...$extra,
        ];
    }

    public function toArray()
    {
        return [
            'host' => $this->host,
            'port' => $this->port,
            'auth_type' => $this->password !== null ? 'password' : 'key',
            'username' => $this->username,
        ];
    }
}
