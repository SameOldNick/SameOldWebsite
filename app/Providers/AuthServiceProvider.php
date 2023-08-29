<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * The roles to map as gates
     *
     * @var array
     */
    protected $roles = [
        'admin',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->registerGates();

        Password::defaults(function () {
            $rule = Password::min(8);

            return $this->app->isProduction()
                        ? $rule->mixedCase()->numbers()->symbols()->rules('uncommon_password')
                        : $rule;
        });
    }

    /**
     * Maps roles to gates
     *
     * @return void
     */
    protected function registerGates()
    {
        foreach ($this->roles as $key => $value) {
            $gate = Arr::isKeyIndex($this->roles, $key) ? $value : $key;
            $role = $value;

            Gate::define($gate, fn (User $user) => $user->hasRoles([$role]));
        }
    }
}
