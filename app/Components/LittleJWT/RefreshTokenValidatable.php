<?php

namespace App\Components\LittleJWT;

use App\Models\RefreshToken;
use LittleApps\LittleJWT\Contracts\Validatable;
use LittleApps\LittleJWT\Validation\Validator;

class RefreshTokenValidatable implements Validatable
{
    public function __construct()
    {
    }

    public function validate(Validator $validator)
    {
        $validator->claimCallback('jti', function ($value) {
            return RefreshToken::where([
                ['jwt_id', '=', $value],
                ['expires_at', '>=', now()],
            ])->exists();
        });
    }
}
