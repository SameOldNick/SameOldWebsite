<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use LittleApps\LittleJWT\Build\Buildables\GuardBuildable;
use LittleApps\LittleJWT\JWT\JsonWebToken;
use LittleApps\LittleJWT\Utils\ResponseBuilder;

class AdminController extends Controller
{
    public function __construct(
        private readonly Application $app
    ) {}

    public function singleSignOn(Request $request, User $user)
    {
        // Based off https://stackoverflow.com/a/26834685/533242

        /**
         * @var JsonWebToken
         */
        $accessToken = Auth::guard('jwt')->buildJwtForUser($user);

        $refreshTokenExpiresAt = Carbon::now()->addDays(7);

        $buildable = new GuardBuildable($user, ['exp' => $refreshTokenExpiresAt]);

        /**
         * @var JsonWebToken
         */
        $refreshToken = $this->app->make('littlejwt.refresh')->create($buildable);

        // Store JTI in database (so refresh tokens can be validated and revoked)
        $user->refreshTokens()->create([
            'jwt_id' => $refreshToken->getPayload()->jti,
            'expires_at' => $refreshTokenExpiresAt,
        ]);

        return view('admin.app', [
            'accessToken' => ResponseBuilder::buildFromJwt($accessToken),
            'refreshToken' => ResponseBuilder::buildFromJwt($refreshToken),
        ]);
    }

    /**
     * Handle the incoming request.
     *
     * @return Response
     */
    public function app(Request $request)
    {
        return view('admin.app');
    }
}
