<?php

namespace App\Components\OAuth\Drivers;

use App\Components\OAuth\Exceptions\OAuthLoginException;
use App\Components\OAuth\Exceptions\UserHasCredentialsException;
use App\Models\OAuthProvider;
use App\Models\User;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\InvalidStateException;

abstract class Driver
{
    public function __construct(
        protected Container $container
    ) {}

    /**
     * Checks if driver is configured.
     *
     * @return bool
     */
    public function isConfigured()
    {
        return Arr::filled($this->getConfig(), ['client_id', 'client_secret']);
    }

    /**
     * Gets the configuration for the driver.
     */
    public function getConfig(): array
    {
        $defaults = [
            'client_id' => '',
            'client_secret' => '',
        ];

        $key = sprintf('oauth.%s', $this->providerName());

        return config($key, $defaults);
    }

    /**
     * Handles redirecting to external OAuth provider.
     *
     * @return mixed
     */
    public function handleRedirect()
    {
        return $this->prepareRedirectResponse()->provider()->redirect();
    }

    /**
     * Handles callback from external OAuth provider.
     *
     * @return mixed
     */
    public function handleCallback()
    {
        try {
            $socialiteUser = $this->provider()->user();
        } catch (InvalidStateException $ex) {
            /**
             * This happens when the provider sent a response back to the app that it wasn't expecting.
             * Technically, this is because there's nothing in the session about the OAuth state.
             * This can be caused by the user using a container/private tab when authenticating on the third-party OAuth provider.
             */

            throw new OAuthLoginException(new InvalidStateException(__('An OAuth response was received that wasn\'t expected.')));
        } catch (Exception $ex) {
            throw new OAuthLoginException($ex);
        }

        return $this->prepareCallbackResponse($socialiteUser)->generateCallbackResponse();
    }

    /**
     * Prepares redirect response.
     * Example: Setting the scopes for the provider.
     *
     * @return $this
     */
    protected function prepareRedirectResponse()
    {
        return $this;
    }

    /**
     * Prepares callback response.
     * Example: Create and login user.
     *
     * @return $this
     */
    protected function prepareCallbackResponse(SocialiteUser $socialiteUser)
    {
        $user = $this->createOrUpdateUser($socialiteUser);

        return $this->login($user);
    }

    /**
     * Logs user in
     *
     * @return $this
     */
    protected function login(User $user)
    {
        Auth::login($user);

        return $this;
    }

    /**
     * Generates callback response
     *
     * @return mixed
     */
    protected function generateCallbackResponse()
    {
        return redirect()->route('user.profile');
    }

    /**
     * Gets Socialite provider.
     *
     * @return AbstractProvider
     */
    protected function provider()
    {
        return Socialite::driver($this->providerName());
    }

    /**
     * Creates or updates user based on Socialite User response.
     */
    protected function createOrUpdateUser(SocialiteUser $oauthUser): User
    {
        // Check if a user with this email exists in the database.
        $existingUser = User::where('email', $oauthUser->getEmail())->first();

        if ($existingUser) {
            // Account with the same email already exists
            // Implement your logic for handling this situation
            // You may link accounts or merge data here.
            if ($existingUser->password) {
                // User must login with their username and password.

                throw new UserHasCredentialsException($existingUser);
            } else {
                $oauthProvider = $this->mapToOAuthProvider($oauthUser);

                $existingUser->oauthProviders()->save($oauthProvider);

                return $existingUser;
            }
        } else {
            // Create a new user account
            $newUser = new User;
            $newUser->name = $oauthUser->getName();
            $newUser->email = $oauthUser->getEmail();
            $newUser->save();

            $oauthProvider = $this->mapToOAuthProvider($oauthUser);

            $newUser->oauthProviders()->save($oauthProvider);

            return $newUser;
        }
    }

    protected function mapToOAuthProvider(SocialiteUser $oauthUser): OAuthProvider
    {
        $oauthProvider = new OAuthProvider;

        $oauthProvider->provider_name = $this->providerName();
        $oauthProvider->provider_id = $oauthUser->getId();
        $oauthProvider->access_token = $oauthUser->token;
        $oauthProvider->refresh_token = $oauthUser->refreshToken;
        $oauthProvider->avatar_url = $oauthUser->getAvatar();
        $oauthProvider->expires_at = now()->addSeconds($oauthUser->expiresIn);

        return $oauthProvider;
    }

    /**
     * Gets name of Socialite provider.
     */
    abstract protected function providerName(): string;
}
