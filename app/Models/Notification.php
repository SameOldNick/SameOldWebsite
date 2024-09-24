<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;

/**
 *
 * @method static \Database\Factories\NotificationFactory factory($count = null, $state = [])
 */
class Notification extends DatabaseNotification {
    use HasFactory;
}
