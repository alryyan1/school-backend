<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; // Import User model
use Hash;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // === Reset and define ONLY the requested permissions (Arabic) ===
        DB::table('role_has_permissions')->delete();
        DB::table('model_has_permissions')->delete();
        Permission::query()->delete();

        $acceptStudent = Permission::firstOrCreate(['name' => 'قبول الطالب']);
        $discount = Permission::firstOrCreate(['name' => 'التخفيض']);
        $assign = Permission::firstOrCreate(['name' => 'التعيين']);


        // === Define Roles and Assign Permissions (restricted to three roles) ===

        // Clean up any roles not in the allowed list
        $allowed = ['admin', 'school manager', 'accountant'];
        Role::whereNotIn('name', $allowed)->delete();

        // Admin: full access
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        // School Manager: manage school-level operations
        $schoolManager = Role::firstOrCreate(['name' => 'school manager']);
        $schoolManager->syncPermissions([$acceptStudent, $discount, $assign]);

        // Accountant: finance-related
        $accountantRole = Role::firstOrCreate(['name' => 'accountant']);
        $accountantRole->syncPermissions([$discount]);


        // --- Optionally ensure a default admin user exists ---
        $admin = User::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Administrator', 'username' => 'admin', 'password' => Hash::make('12345678')
        ]);
        $admin->syncRoles(['admin']);
    }
}