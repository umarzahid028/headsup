<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessing;


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

       

        
        // Force sync queue for broadcasting
        if (app()->environment('local', 'staging', 'production')) {
            Queue::before(function (JobProcessing $event) {
                if (isset($event->job->payload()['data']['command']) && 
                    str_contains($event->job->payload()['data']['command'], 'BroadcastEvent')) {
                    Log::info('Broadcasting job detected, processing immediately', [
                        'job' => $event->job->getName(),
                        'connection' => $event->connectionName,
                        'payload' => $event->job->payload(),
                    ]);
                }
            });
        }
    }
}
