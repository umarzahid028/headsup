<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test vehicle notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating test vehicle...');

        try {
            // First check current unread notifications
            $managers = \App\Models\User::role(['Sales Manager', 'Recon Manager'])->get();
            foreach ($managers as $manager) {
                $this->info("Current unread notifications for {$manager->name}: " . 
                    $manager->unreadNotifications()
                        ->whereIn('type', [
                            'App\\Notifications\\NewVehicleArrival',
                            'App\\Notifications\\NewVehicleImported'
                        ])
                        ->count()
                );
            }

            $vehicle = \App\Models\Vehicle::create([
                'stock_number' => 'TEST' . time(),
                'vin' => 'TEST' . time() . 'VIN',
                'year' => 2024,
                'make' => 'Test',
                'model' => 'Model'
            ]);

            $this->info('Vehicle created successfully.');

            $this->info('Found ' . $managers->count() . ' managers to notify.');

            foreach ($managers as $manager) {
                $manager->notify(new \App\Notifications\NewVehicleArrival($vehicle));
                $this->info("Notification sent to {$manager->name} ({$manager->email})");
            }

            // Check unread notifications after sending
            $this->info('Checking unread notifications after sending...');
            foreach ($managers as $manager) {
                $unreadCount = $manager->unreadNotifications()
                    ->whereIn('type', [
                        'App\\Notifications\\NewVehicleArrival',
                        'App\\Notifications\\NewVehicleImported'
                    ])
                    ->count();
                $this->info("Unread notifications for {$manager->name}: {$unreadCount}");
                
                // List all notifications
                $this->info("All notifications for {$manager->name}:");
                foreach ($manager->notifications as $notification) {
                    $this->info("- Type: {$notification->type}, Read: " . ($notification->read_at ? 'Yes' : 'No'));
                }
            }

            $this->info('Test completed successfully.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
