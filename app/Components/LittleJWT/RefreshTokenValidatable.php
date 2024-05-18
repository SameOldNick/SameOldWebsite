<?php

namespace App\Components\LittleJWT;

use App\Models\RefreshToken;
use LittleApps\LittleJWT\Validation\Validator;

class RefreshTokenValidatable
{
    public function __invoke(Validator $validator)
    {
        $validator->claimCallback('jti', function ($value) {
            return RefreshToken::where([
                ['jwt_id', '=', $value],
                ['expires_at', '>=', now()],
            ])->exists();
        });
    }
}
