<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Str;
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
        $this->registerRoles();
        $this->registerRoleGroups();
    }

    /**
     * Registers each role as gate
     *
     * @return void
     */
    protected function registerRoles() {
        $roles = config('roles.roles', []);

        foreach ($roles as $role) {
            $id = $this->generateGateAbility('role', $role['id']);

            Gate::define($id, fn (User $user) => $user->hasRoles([$id]));
        }
    }

    /**
     * Register role groups as gates
     *
     * @return void
     */
    protected function registerRoleGroups() {
        $groups = config('roles.groups', []);

        foreach ($groups as $group) {
            $id = $this->generateGateAbility('roles', $group['id']);
            $roles = $group['roles'];

            Gate::define($id, fn (User $user) => $user->hasRoles($roles));
        }
    }

    protected function generateGateAbility(string $prefix, string $name) {
        return Str::kebab(sprintf('%s %s', $prefix, Str::replace('_', '-', $name)));
    }
}
