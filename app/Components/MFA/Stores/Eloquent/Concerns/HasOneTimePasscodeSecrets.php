<?php

namespace App\Components\MFA\Stores\Eloquent\Concerns;

use App\Components\MFA\Stores\Eloquent\Models\OneTimePasscodeSecret;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasOneTimePasscodeSecrets
{
    /**
     * Get the secret associated with the user.
     */
    public function oneTimePasscodeSecrets(): HasOne
    {
        return $this->hasOne(OneTimePasscodeSecret::class, 'user_id');
    }
}
