<?php

namespace App\Http\Controllers\Main\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Rules\PostalCodeAlpha3;
use App\Rules\State as StateRule;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Displays users profile
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function view(Request $request)
    {
        return view('main.user.profile', [
            'countries' => Country::sortedByCountry(),
        ]);
    }

    /**
     * Updates the users profile information
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignoreModel($user),
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

        if (isset($validated['state'])) {
            $state = $country->states->firstWhere('code', $validated['state']);
            $user->state()->associate($state);
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
}
