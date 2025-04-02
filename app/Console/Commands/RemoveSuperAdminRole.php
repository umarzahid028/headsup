<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class RemoveSuperAdminRole extends Command
{
    protected $signature = 'role:remove-super-admin';
    protected $description = 'Remove the super-admin role from the database';

    public function handle()
    {
        $role = Role::where('name', 'super-admin')->first();
        
        if ($role) {
            $role->delete();
            $this->info('Super-admin role has been removed successfully.');
        } else {
            $this->info('Super-admin role does not exist.');
        }
        
        return 0;
    }
} 