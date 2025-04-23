<?php

namespace App\Http\Controllers\Main\User;

use App\Components\OAuth\Facades\OAuth;
use App\Http\Controllers\Controller;
use App\Models\OAuthProvider;
use Illuminate\Http\Request;

class ConnectedAccountsController extends Controller
{
    /**
     * Displays all OAuth providers and status (connected/disconnected)
     */
    public function index(Request $request)
    {
        $providers = OAuth::configured();

        $data = [];

        if ($request->session()->has('oauth.connecting')) {
            $provider = $request->session()->get('oauth.connecting');

            $data['success'] = __('Your account has been connected to :provider.', [
                'provider' => OAuth::provider($provider)->getName(),
            ]);
        } elseif ($request->session()->has('success')) {
            $data['success'] = $request->session()->get('success');
        }

        return view('main.user.connected-accounts.index', compact('providers'))->with($data);
    }

    /**
     * Starts connecting for OAuth provider
     *
     * @return mixed
     */
    public function connect(Request $request, string $provider)
    {
        if (! in_array($provider, OAuth::configured())) {
            return redirect()->back()->withErrors(['provider' => 'The OAuth provider is invalid or not configured properly.']);
        }

        $request->session()->flash('oauth.connecting', $provider);

        return redirect()->route('oauth.redirect', ['provider' => $provider]);
    }

    /**
     * Confirms disconnecting from OAuth provider
     *
     * @return mixed
     */
    public function disconnect(Request $request, OAuthProvider $provider)
    {
        if (is_null($request->user()->password) && $request->user()->oauthProviders->count() <= 1) {
            return redirect()->route('user.connected-accounts')->withErrors([
                'provider' => __('You cannot disconnect the last connected account. Please set a password first.'),
            ]);
        }

        $this->authorize('delete', $provider);

        $name = OAuth::provider($provider->provider_name)->getName();

        return view('main.user.connected-accounts.disconnect', compact('name', 'provider'));
    }

    /**
     * Disconnects from OAuth provider
     */
    public function destroy(OAuthProvider $provider)
    {
        $this->authorize('delete', $provider);

        // TODO: Try to deactivate on OAuth provider side
        $provider->delete();

        return redirect()->route('user.connected-accounts')->with(
            'success',
            __('Your account has been disconnected from :provider.', [
                'provider' => OAuth::provider($provider->provider_name)->getName(),
            ])
        );
    }
}
