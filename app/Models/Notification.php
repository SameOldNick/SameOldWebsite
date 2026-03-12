<?php

namespace App\Models;

use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $type
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property array $data
 * @property ?Carbon $read_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class Notification extends DatabaseNotification
{
    /** @use HasFactory<NotificationFactory> */
    use HasFactory;
}
