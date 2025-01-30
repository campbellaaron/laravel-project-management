<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Role::class => \App\Policies\RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define the "create-roles" ability
        Gate::define('create-roles', function ($user) {
            return $user->hasAnyRole(['super-admin', 'admin']);  // Only admins can create roles
        });

        Gate::define('create-users', function ($user) {
            return $user->hasAnyRole('super-admin', 'admin');
        });

        Gate::define('edit-users', function ($user) {
            return $user->hasRole('super-admin', 'admin');
        });

        Gate::define('delete-users', function ($user) {
            return $user->hasPermissionTo('delete users');
        });
    }
}
