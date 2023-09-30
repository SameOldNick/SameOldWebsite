<?php

namespace App\Components\Placeholders\Builders;

use App\Components\Placeholders\PlaceholderCollection;
use Illuminate\Http\Request;

class RequestBuilder {
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function __invoke() {
        return [
            'user-agent' => fn() => $this->request->userAgent(),
            'ip-address' => fn() => $this->request->ip(),
            'ip' => fn() => $this->request->ip(),
        ];
    }
}
