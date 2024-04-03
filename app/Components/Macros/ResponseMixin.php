<?php

namespace App\Components\Macros;

use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ResponseMixin
{
    public function fromTranslation()
    {
        return function ($key, array $extra = [], $status = 200, array $headers = []) {
            $keys = Str::of($key)->explode('.');

            $responseKey = $keys->slice($keys->count() > 1 ? 1 : 0)->join('.');

            return Response::withMessage(trans($key), $extra + ['response' => $responseKey], $status, $headers);
        };
    }

    public function withMessage()
    {
        return function ($message, array $extra = [], $status = 200, array $headers = []) {
            return response($extra + ['message' => $message], $status, $headers);
        };
    }
}
