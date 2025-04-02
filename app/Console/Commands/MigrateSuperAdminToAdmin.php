<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class MigrateSuperAdminToAdmin extends Command
{
    protected $signature = 'role:migrate-superadmin';
    protected $description = 'Migrate users from super-admin to admin role';

    public function handle()
    {
        // Get all users with super-admin role
        $users = User::role('super-admin')->get();
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        $count = 0;
        foreach ($users as $user) {
            $user->removeRole('super-admin');
            $user->assignRole('admin');
            $count++;
        }
        
        if ($count > 0) {
            $this->info("{$count} users migrated from super-admin to admin role.");
        } else {
            $this->info("No users found with super-admin role.");
        }
        
        return 0;
    }
} 