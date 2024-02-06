<?php

namespace App\Components\MFA\Events\OTP;

use App\Components\MFA\Contracts\MultiAuthenticatable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BackupCodeVerified
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly MultiAuthenticatable $authenticatable,
    ) {
        //
    }
}
