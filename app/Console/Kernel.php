<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     * 
     */

    protected $commands = [
        ImportVehiclesCsv::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Run the vehicle import every hour, archive processed files
        $schedule->command('import:vehicles-csv --archive')
                ->everyFiveMinutes()
                ->appendOutputTo(storage_path('logs/vehicle-import.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 