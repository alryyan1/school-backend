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

        // Create the 5 new permissions
        $acceptStudent = Permission::firstOrCreate(['name' => 'قبول الطالب']);
        $addStudent = Permission::firstOrCreate(['name' => 'اضافه طالب']);
        $editStudent = Permission::firstOrCreate(['name' => 'تعديل بيانات الطالب']);
        $payFees = Permission::firstOrCreate(['name' => 'سداد رسوم الطالب']);
        $assignStudent = Permission::firstOrCreate(['name' => 'تعيين الطالب']);

        // === Define Roles and Assign Permissions ===

        // Clean up any existing roles
        Role::query()->delete();

        // Admin: full access to all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions([
            $acceptStudent, 
            $addStudent, 
            $editStudent, 
            $payFees, 
            $assignStudent
        ]);

        // School Manager: can accept, add, edit, and assign students
        $schoolManager = Role::firstOrCreate(['name' => 'school manager']);
        $schoolManager->syncPermissions([
            $acceptStudent, 
            $addStudent, 
            $editStudent, 
            $assignStudent
        ]);

        // Accountant: can add students and handle fee payments
        $accountantRole = Role::firstOrCreate(['name' => 'accountant']);
        $accountantRole->syncPermissions([
            $addStudent, 
            $payFees
        ]);

        // Teacher: can only edit student data
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $teacherRole->syncPermissions([
            $editStudent
        ]);

        // --- Ensure a default admin user exists ---
        $admin = User::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Administrator', 
            'username' => 'admin', 
            'password' => Hash::make('12345678')
        ]);
        $admin->syncRoles(['admin']);
    }
}