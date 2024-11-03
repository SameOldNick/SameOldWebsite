<?php

namespace App\Components\OAuth\Handlers;

use Laravel\Socialite\Contracts\User as SocialUser;
use App\Components\OAuth\Contracts\OAuthFlowHandler as OAuthFlowHandlerContract;
use App\Components\OAuth\Drivers\Driver;
use App\Components\OAuth\Facades\OAuth;
use App\Models\OAuthProvider;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OAuthFlowHandler implements OAuthFlowHandlerContract
{
    public function __construct(
        public readonly Driver $provider
    ) {}

    /**
     * @inheritDoc
     */
    public function handleOAuthRedirect()
    {
        return $this->provider->provider()->redirect();
    }

    /**
     * @inheritDoc
     */
    public function handleOAuthCallback(SocialUser $socialUser)
    {
        if ($user = $this->lookupUser($socialUser)) {
            if ($this->isLoggedIn()) {
                return $this->redirectToRoute('user.profile');
            } else {
                return $this->login($user)->redirectToRoute('user.profile');
            }
        } else {
            if (!$this->isLoggedIn()) {
                if ($this->canCreateUser($socialUser)) {
                    $user = $this->registerUser($socialUser);

                    $this->associateWithUser($user, $socialUser)->login($user);

                    return $this->redirectToRoute('user.profile');
                } else {
                    return $this->redirectToRoute('login')->withInfo(
                        'Please log in with your password to connect your account with ' . $this->provider->getName() . '.'
                    );
                }
            } else {
                $user = $this->currentUser();

                $this->associateWithUser($user, $socialUser, true);

            }
        }
    }

    /**
     * Checks if user is logged in
     *
     * @return boolean
     */
    protected function isLoggedIn(): bool
    {
        return Auth::check();
    }

    /**
     * Gets the current user
     *
     * @return User
     */
    protected function currentUser(): User
    {
        return Auth::user();
    }

    /**
     * Login user
     *
     * @param User $user
     * @return $this
     */
    protected function login(User $user): static
    {
        Auth::login($user);

        return $this;
    }

    /**
     * Creates response to redirect to route
     *
     * @param string $route
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToRoute(string $route)
    {
        return redirect()->route($route);
    }

    /**
     * Look up user from social user
     *
     * @param SocialUser $socialUser
     * @return User|null User or null if not found
     */
    protected function lookupUser(SocialUser $socialUser): ?User
    {
        $provider = OAuthProvider::where('provider_id', $socialUser->getId())->where('provider_name', $this->provider->providerName())->first();

        // $provider->user will be null if user is deleted.
        return $provider && $provider->user ? $provider->user : null;
    }

    /**
     * Checks that new user can be created from social user.
     *
     * @param SocialUser $socialUser
     * @return boolean
     */
    protected function canCreateUser(SocialUser $socialUser): bool
    {
        return User::withTrashed()->where('email', $socialUser->getEmail())->count() === 0;
    }

    /**
     * Registers user based on Socialite User response.
     */
    protected function registerUser(SocialUser $socialUser): User
    {
        // Create a new user account
        $newUser = new User;
        $newUser->name = $socialUser->getName();
        $newUser->email = $socialUser->getEmail();
        $newUser->save();

        // Needs to fire so things like emails can be sent
        event(new Registered($newUser));

        return $newUser;
    }

    /**
     * Associates social user with user
     *
     * @param User $user
     * @param SocialUser $socialUser
     * @param boolean $replace If true, the previous social user is deleted
     * @return $this
     */
    protected function associateWithUser(User $user, SocialUser $socialUser, bool $replace = false)
    {
        if ($replace) {
            $user->oauthProviders()->where('provider_name', $this->provider->providerName())->delete();
        }

        $oauthProvider = $this->mapToOAuthProvider($socialUser);

        $user->oauthProviders()->save($oauthProvider);

        return $this;
    }

    /**
     * Creates OAuthProvider model from social user
     *
     * @param SocialUser $socialUser
     * @return OAuthProvider
     */
    protected function mapToOAuthProvider(SocialUser $socialUser): OAuthProvider
    {
        $oauthProvider = new OAuthProvider;

        $oauthProvider->provider_name = $this->provider->providerName();
        $oauthProvider->provider_id = $socialUser->getId();
        $oauthProvider->access_token = $socialUser->token;
        $oauthProvider->refresh_token = $socialUser->refreshToken;
        $oauthProvider->avatar_url = $socialUser->getAvatar();
        $oauthProvider->expires_at = now()->addSeconds($socialUser->expiresIn);

        return $oauthProvider;
    }
}
