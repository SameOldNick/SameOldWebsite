<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Models\Country;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'show' => 'sometimes|in:active,inactive,both',
            'sort' => 'sometimes|in:id,name,email',
            'order' => 'sometimes|in:asc,desc',
        ]);

        $query = User::query();

        if ($request->has('show')) {
            if ($request->show === 'inactive') {
                $query = $query->onlyTrashed();
            } elseif ($request->show === 'both') {
                $query = $query->withTrashed();
            }
        }

        $query = $query->orderBy($request->get('sort', 'id'), $request->get('order', 'asc'));

        return new UserCollection($query->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (! empty($request->country_code)) {
            $country = Country::firstWhere(['code' => $request->country_code]);

            if (! empty($request->state_code)) {
                $state = $country->states()->where(['code' => $request->state_code])->first();

                $user->state()->associate($state);
            }

            $user->country()->associate($country);
        }

        $user->save();

        return $user->makeVisible(['created_at', 'deleted_at']);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $user->makeVisible(['created_at', 'deleted_at']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->name = $request->name;
        $user->email = $request->email;

        if (! empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        if (! empty($request->country_code)) {
            $country = Country::firstWhere(['code' => $request->country_code]);

            if (! empty($request->state_code)) {
                $state = $country->states()->where(['code' => $request->state_code])->first();

                $user->state()->associate($state);
            }

            $user->country()->associate($country);
        }

        $roles = Role::whereIn('role', $request->roles)->get();

        $user->roles()->sync($roles);

        $user->save();

        return $user->makeVisible(['created_at', 'deleted_at']);
    }

    /**
     * Restores a user
     *
     * @param User $user
     * @return array
     */
    public function restore(User $user)
    {
        $user->restore();

        return [
            'success' => __('User with e-mail ":email" was unlocked.', ['email' => $user->email]),
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return [
            'success' => __('User with e-mail ":email" was locked.', ['email' => $user->email]),
        ];
    }
}
