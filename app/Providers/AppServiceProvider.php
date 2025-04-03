<?php

namespace App\Providers;

use App\Models\Transporter;
use App\Models\Vendor;
use App\Observers\TransporterObserver;
use App\Observers\VendorObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the role directive
        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        Transporter::observe(TransporterObserver::class);
        Vendor::observe(VendorObserver::class);
    }
}
