<?php

namespace App\View\Components\ConnectedAccounts;

use App\Components\OAuth\Facades\OAuth;
use App\Models\OAuthProvider;
use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class ProviderItem extends Component
{
    public readonly string $name;
    public readonly string $icon;
    public readonly ?OAuthProvider $provider;

    protected array $iconMappings = [
        'google' => 'fab-google',
        'github' => 'fab-github',
        'twitter' => 'fab-x-twitter',
    ];

    /**
     * Create a new component instance.
     */
    public function __construct(
        public readonly string $providerName,
    ) {
        $this->name = $this->getName($providerName);
        $this->icon = $this->getIcon($providerName);
        $this->provider = Auth::hasUser() ? $this->getProvider(Auth::user()) : null;
    }

    /**
     * Gets the name of the provider
     *
     * @param string $providerName
     * @return string
     */
    public function getName(string $providerName): string
    {
        return OAuth::driver($providerName)->getName();
    }

    /**
     * Gets the icon ID
     *
     * @param string $providerName
     * @return string|null
     */
    public function getIcon(string $providerName): ?string
    {
        return isset($this->iconMappings[$providerName]) ? $this->iconMappings[$providerName] : null;
    }

    /**
     * Checks if provider is connected
     *
     * @return boolean
     */
    public function isConnected(): bool
    {
        return !is_null($this->provider);
    }

    /**
     * Gets URL to connect to OAuth provider
     *
     * @return string
     */
    public function connectUrl(): string
    {
        return route('user.connected-accounts.connect', ['provider' => $this->providerName]);
    }

    /**
     * Gets URL to disconnect from OAuth provider
     *
     * @return string
     */
    public function disconnectUrl(): string
    {
        return $this->provider ? route('user.connected-accounts.disconnect', ['provider' => $this->provider]) : '';
    }

    /**
     * Gets provider associated with user
     *
     * @param User|null $user
     * @return OAuthProvider
     */
    protected function getProvider(User $user)
    {
        return $user->oauthProviders()->where('provider_name', $this->providerName)->first();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.connected-accounts.provider-item');
    }
}
