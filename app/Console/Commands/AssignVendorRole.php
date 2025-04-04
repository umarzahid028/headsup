<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AssignVendorRole extends Command
{
    protected $signature = 'user:assign-vendor-role {email} {type=onsite : The type of vendor (onsite/offsite)}';
    protected $description = 'Assign vendor role to a user';

    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->argument('type');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }
        
        $role = $type === 'offsite' ? Role::OFFSITE_VENDOR : Role::ONSITE_VENDOR;
        
        $user->update(['role' => $role]);
        
        $this->info("Successfully assigned {$role->label()} role to {$user->name}");
        
        return 0;
    }
} 