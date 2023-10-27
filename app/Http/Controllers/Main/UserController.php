<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Rules\PostalCodeAlpha3;
use App\Rules\State as StateRule;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Displays users profile
     *
     * @return mixed
     */
    public function viewProfile(Request $request)
    {
        return view('main.user.profile', [
            'countries' => Country::sortedByCountry(),
        ]);
    }

    /**
     * Updates the users profile information
     *
     * @param Request $request
     * @return mixed
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'address1' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'country' => 'required|exists:countries,code',
            'state' => [
                'nullable',
                new StateRule('country'),
            ],
            'city' => 'nullable|string|max:255',
            'postal_code' => [
                'nullable',
                new PostalCodeAlpha3('country'),
            ],
        ]);

        // These are for relationships and will be set after
        $ignoreKeys = ['country', 'state'];

        $user->forceFill(Arr::except($validated, $ignoreKeys));

        $country = Country::find($validated['country']);

        $user->country()->associate($country);

        if (array_key_exists('state', $validated)) {
            $user->state()->dissociate();
            $user->setAttribute('state', null);

            if (! is_null($validated['state'])) {
                // Determine if it's state_id or state that's to be set (not both)
                if ($user->isStateAssociated()) {
                    $user->state()->associate($country->states->firstWhere('code', $validated['state']));
                } else {
                    $user->setAttribute('state', $validated['state']);
                }
            }
        }

        if ($user->isDirty()) {
            $user->save();

            $message = __('User information was updated.');
        } else {
            $message = __('User information was not changed.');
        }

        return view('main.user.profile', [
            'countries' => Country::sortedByCountry(),
            'success' => $message,
        ]);
    }

    /**
     * Displays change password page
     *
     * @return mixed
     */
    public function viewChangePassword(Request $request)
    {
        return view('main.user.change-password');
    }

    /**
     * Changes users password
     *
     * @return mixed
     */
    public function updateChangePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => !is_null($request->user()->password) ? 'required|current_password' : '',
            'new_password' => Password::required(),
        ]);

        // TODO: Send notification that password was changed.
        tap($request->user(), function ($user) use ($validated) {
            $user->password = Hash::make($validated['new_password']);
        })->save();

        Auth::logout();

        $message = __('Your password was updated. Please login again.');

        return redirect()->route('login')->with(['success' => $message]);
    }
}
