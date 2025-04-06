<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SalesTeam;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TestSalesTeamFlow extends Command
{
    protected $signature = 'test:sales-team-flow';
    protected $description = 'Test the sales team creation flow';

    public function handle()
    {
        $this->info('Starting sales team creation flow test...');

        DB::beginTransaction();

        try {
            // Test data
            $testData = [
                'name' => 'Test Sales Member',
                'email' => 'test.sales@example.com',
                'password' => 'password123',
                'phone' => '1234567890',
                'position' => 'Sales Representative',
                'bio' => 'Test bio information',
                'is_active' => true,
            ];

            // Clean up any existing test data
            $this->info('Cleaning up existing test data...');
            if ($existingUser = User::where('email', $testData['email'])->first()) {
                $existingUser->delete();
                $this->info('Deleted existing user');
            }
            if ($existingSalesTeam = SalesTeam::where('email', $testData['email'])->first()) {
                $existingSalesTeam->delete();
                $this->info('Deleted existing sales team member');
            }

            // Create user account
            $this->info('Creating user account...');
            $user = User::create([
                'name' => $testData['name'],
                'email' => $testData['email'],
                'password' => $testData['password'],
                'phone' => $testData['phone'],
            ]);

            // Assign sales team role
            $this->info('Assigning sales team role...');
            $user->assignRole('Sales Team');

            // Create sales team member
            $this->info('Creating sales team member...');
            $salesTeam = SalesTeam::create([
                'name' => $testData['name'],
                'email' => $testData['email'],
                'phone' => $testData['phone'],
                'position' => $testData['position'],
                'bio' => $testData['bio'],
                'is_active' => $testData['is_active'],
            ]);

            // Verify creation
            $this->info('Verifying creation...');
            $this->table(
                ['Field', 'User Value', 'Sales Team Value'],
                [
                    ['Name', $user->name, $salesTeam->name],
                    ['Email', $user->email, $salesTeam->email],
                    ['Role', $user->hasRole('Sales Team') ? 'Yes' : 'No', 'N/A'],
                    ['Position', 'N/A', $salesTeam->position],
                ]
            );

            // Verify password hashing
            $this->info('Verifying password hashing...');
            $this->info('User password is hashed: ' . (Hash::check($testData['password'], $user->password) ? 'Yes' : 'No'));

            DB::rollBack();
            $this->info('Test completed successfully! (Changes rolled back)');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Test failed: ' . $e->getMessage());
        }
    }
} 