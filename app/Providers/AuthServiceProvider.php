<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
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
    protected function registerRoles()
    {
        $roles = config('roles.roles', []);

        foreach ($roles as $role) {
            $roleId = $role['id'];

            Gate::define($this->generateGateAbility('role', $roleId), fn (User $user) => $user->hasAllRoles([$roleId]));
        }
    }

    /**
     * Register role groups as gates
     *
     * @return void
     */
    protected function registerRoleGroups()
    {
        $groups = config('roles.groups', []);

        foreach ($groups as $group) {
            $roles = $group['roles'];

            Gate::define($this->generateGateAbility('all-roles', $group['id']), fn (User $user) => $user->hasAllRoles($roles));
            Gate::define($this->generateGateAbility('any-roles', $group['id']), fn (User $user) => $user->hasAnyRoles($roles));
        }
    }

    protected function generateGateAbility(string $prefix, string $name)
    {
        return Str::kebab(sprintf('%s %s', $prefix, Str::replace('_', '-', $name)));
    }
}
