<?php

namespace App\Http\Controllers\Main\User;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Components\OAuth\Facades\OAuth;
use App\Components\SweetAlert\SweetAlertBuilder;
use App\Components\SweetAlert\SweetAlerts;
use App\Models\OAuthProvider;

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
                'provider' => OAuth::driver($provider)->getName(),
            ]);
        } else if ($request->session()->has('success')) {
            $data['success'] = $request->session()->get('success');
        }

        return view('main.user.connected-accounts.index', compact('providers'))->with($data);
    }

    /**
     * Starts connecting for OAuth provider
     *
     * @param Request $request
     * @param string $provider
     * @return mixed
     */
    public function connect(Request $request, string $provider)
    {
        if (!in_array($provider, OAuth::configured())) {
            return redirect()->back()->withErrors(['provider' => 'The OAuth provider is invalid or not configured properly.']);
        }

        $request->session()->flash('oauth.connecting', $provider);

        return redirect()->route('oauth.redirect', ['driver' => $provider]);
    }

    /**
     * Confirms disconnecting from OAuth provider
     *
     * @param Request $request
     * @param OAuthProvider $provider
     * @return mixed
     */
    public function disconnect(Request $request, OAuthProvider $provider)
    {
        $this->authorize('delete', $provider);

        $name = OAuth::driver($provider->provider_name)->getName();

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
                'provider' => OAuth::driver($provider->provider_name)->getName()
            ])
        );
    }
}
