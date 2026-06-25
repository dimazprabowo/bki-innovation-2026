<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@app.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'phone' => '021-1234566',
                'position' => 'Super Administrator',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if (!$superAdmin->hasRole('super admin')) {
            $superAdmin->assignRole('super admin');
        }

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@app.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'phone' => '021-1234567',
                'position' => 'System Administrator',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // User
        $user = User::firstOrCreate(
            ['email' => 'user@app.com'],
            [
                'name' => 'Sample User',
                'password' => Hash::make('password'),
                'phone' => '021-1234568',
                'position' => 'Staff',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if (!$user->hasRole('user')) {
            $user->assignRole('user');
        }

        // Admin Data
        $adminData = User::firstOrCreate(
            ['email' => 'admindata@app.com'],
            [
                'name' => 'Admin Data',
                'password' => Hash::make('password'),
                'phone' => '021-1234569',
                'position' => 'Data Entry Master Data',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if (!$adminData->hasRole('admin data')) {
            $adminData->assignRole('admin data');
        }

        // Approver
        $approver = User::firstOrCreate(
            ['email' => 'approver@app.com'],
            [
                'name' => 'Approver',
                'password' => Hash::make('password'),
                'phone' => '021-1234570',
                'position' => 'Approval Officer',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if (!$approver->hasRole('approver')) {
            $approver->assignRole('approver');
        }

        // Admin Project
        $adminProject = User::firstOrCreate(
            ['email' => 'adminproject@app.com'],
            [
                'name' => 'Admin Project',
                'password' => Hash::make('password'),
                'phone' => '021-1234571',
                'position' => 'Project Manager',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if (!$adminProject->hasRole('admin project')) {
            $adminProject->assignRole('admin project');
        }
    }
}
