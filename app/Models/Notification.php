<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @property string $id
 * @property string $type
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property array $data
 * @property ?\Illuminate\Support\Carbon $read_at
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 *
 * @method static \Database\Factories\NotificationFactory factory($count = null, $state = [])
 */
class Notification extends DatabaseNotification
{
    use HasFactory;
}
