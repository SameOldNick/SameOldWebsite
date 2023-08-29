<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use LittleApps\LittleJWT\LittleJWT;
use LittleApps\LittleJWT\Utils\ResponseBuilder;
use LittleApps\LittleJWT\Build\Buildables\GuardBuildable;

class AuthController extends Controller
{
    public function __construct(
        private LittleJWT $jwt
    )
    {
        //
    }

    /**
     * Refreshes access token
     *
     * @param Request $request
     * @return array
     */
    public function refresh(Request $request) {
        $buildable = new GuardBuildable($request->user());

        $accessToken = $this->jwt->createJWT([$buildable, 'build']);

        return ResponseBuilder::buildFromJwt($accessToken);
    }
}
