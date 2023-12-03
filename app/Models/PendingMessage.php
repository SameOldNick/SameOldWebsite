<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class PendingMessage extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'message',
        'expires_at',
    ];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function useDefaultExpiresAt()
    {
        $this->expires_at = static::getDefaultExpiresAt();

        return $this;
    }

    public function generateUrl()
    {
        return URL::temporarySignedRoute('contact.confirm', $this->expires_at, ['pendingMessage' => $this]);
    }

    public static function getDefaultExpiresAt()
    {
        return now()->addHours(2);
    }
}
