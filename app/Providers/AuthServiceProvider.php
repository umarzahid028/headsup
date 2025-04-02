<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // Define permissions for user management
        Gate::define('edit users', function ($user) {
            return $user->hasPermissionTo('edit users');
        });

        // Define gate for approving vendor estimates
        Gate::define('approve-estimates', function ($user) {
            return $user->hasAnyRole(['admin', 'manager', 'sales_manager', 'recon_manager']);
        });
    }
}
