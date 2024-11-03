<?php

namespace App\Components\OAuth\Drivers;

use App\Components\OAuth\Contracts\CallbackHandler;
use App\Components\OAuth\Contracts\OAuthFlowHandler;
use App\Components\OAuth\Exceptions\OAuthLoginException;
use App\Components\OAuth\Exceptions\UserHasCredentialsException;
use App\Models\OAuthProvider;
use App\Models\User;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
     * Gets readable name of provider.
     *
     * @return string
     */
    public function getName(): string
    {
        $providerName = $this->providerName();

        return trans()->has("oauth.providers.{$providerName}") ? trans("oauth.providers.{$providerName}") : Str::headline($providerName);
    }

    /**
     * Handles redirecting to external OAuth provider.
     *
     * @return mixed
     */
    public function handleRedirect()
    {
        return $this->prepareRedirect($this->createHandler())->handleOAuthRedirect();
    }

    /**
     * Handles callback from external OAuth provider.
     *
     * @return mixed
     */
    public function handleCallback()
    {
        $socialUser = $this->getSocialUser();

        return $this->prepareCallback($this->createHandler(), $socialUser)->handleOAuthCallback($socialUser);
    }

    /**
     * Gets the social user
     *
     * @return SocialiteUser
     * @throws OAuthLoginException Thrown if unable to get user
     */
    protected function getSocialUser(): SocialiteUser
    {
        try {
            return $this->provider()->user();
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
    }

    /**
     * Creates flow handler
     *
     * @return OAuthFlowHandler
     */
    protected function createHandler(): OAuthFlowHandler
    {
        return $this->container->make(OAuthFlowHandler::class, ['provider' => $this]);
    }

    /**
     * Prepares handler redirect response.
     * Example: Setting the scopes for the provider.
     *
     * @return OAuthFlowHandler
     */
    protected function prepareRedirect(OAuthFlowHandler $handler): OAuthFlowHandler
    {
        return $handler;
    }

    /**
     * Prepares handler for callback.
     *
     * @param OAuthFlowHandler $handler
     * @param SocialiteUser $socialUser
     * @return OAuthFlowHandler
     */
    protected function prepareCallback(OAuthFlowHandler $handler, SocialiteUser $socialUser): OAuthFlowHandler
    {
        return $handler;
    }

    /**
     * Gets Socialite provider.
     *
     * @return AbstractProvider
     */
    public function provider()
    {
        return Socialite::driver($this->providerName());
    }

    /**
     * Gets name of Socialite provider.
     */
    abstract public function providerName(): string;
}
