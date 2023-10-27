<?php

namespace App\Components\OAuth\Socialite;

use Illuminate\Foundation\Application;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\SocialiteManager as BaseSocialiteManager;

class SocialiteManager extends BaseSocialiteManager
{
    use ForwardsCalls;

    static $aliases = [
        'twitter' => 'twitter-oauth-2'
    ];

    public function __construct(
        Application $app
    ) {
        parent::__construct($app);

        $this->setContainer($app);
    }

    /**
     * Build an OAuth 2 provider instance.
     *
     * @param  string  $provider
     * @param  array  $config
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    public function buildProvider($provider, $ignored)
    {
        $name = $this->determineProviderName($provider);

        $config = $this->getConfig($name);

        return parent::buildProvider($provider, $config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\One\AbstractProvider|\Laravel\Socialite\Two\AbstractProvider
     */
    protected function createTwitterDriver()
    {
        return $this->createTwitterOAuth2Driver();
    }

    protected function determineProviderName(string $class) {
        $base = class_basename($class);

        return Str::kebab(Str::remove('Provider', $base));
    }

    protected function getConfig(string $name) {
        $config = $this->config->get("oauth.{$name}", []);

        if (empty($config) && $this->hasProviderAlias($name)) {
            $alias = $this->getProviderAlias($name);
            $config = $this->config->get("oauth.{$alias}", []);
        }

        if (!isset($config['redirect'])) {
            $config['redirect'] = $this->getCallbackUrl($name);
        }

        return $config;
    }

    protected function hasProviderAlias(string $name) {
        return \array_key_exists($name, static::$aliases);
    }

    protected function getProviderAlias(string $name) {
        return static::$aliases[$name];
    }

    protected function getCallbackUrl(string $name) {
        return route("oauth.callback.{$name}");
    }
}