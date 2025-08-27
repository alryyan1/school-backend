<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create superadmin user (or update if exists)
        $superadmin = User::updateOrCreate(['username' => 'superadmin'], [
            'name' => 'Super Administrator',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('12345678'),
        ]);
        
        // Assign admin role to superadmin
        $superadmin->syncRoles(['admin']);
        
        // Create regular admin user as backup (or update if exists)
        $admin = User::updateOrCreate(['username' => 'admin'], [
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345678'),
        ]);
        
        // Assign admin role to admin
        $admin->syncRoles(['admin']);
        
        // Create useradmin user (or update if exists)
        $useradmin = User::updateOrCreate(['username' => 'useradmin'], [
            'name' => 'User Administrator',
            'email' => 'useradmin@example.com',
            'password' => Hash::make('12345678'),
        ]);
        
        // Assign admin role to useradmin
        $useradmin->syncRoles(['admin']);
    }
}
