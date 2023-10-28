<?php

namespace App\Http\Controllers\Main\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    /**
     * Displays change password page
     *
     * @return mixed
     */
    public function view(Request $request)
    {
        return view('main.user.change-password');
    }

    /**
     * Changes users password
     *
     * @return mixed
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ! is_null($request->user()->password) ? 'required|current_password' : '',
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
