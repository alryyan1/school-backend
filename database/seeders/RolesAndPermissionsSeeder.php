<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; // If assigning roles to existing users

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define Permissions
        // Students
        Permission::firstOrCreate(['name' => 'view students']);
        Permission::firstOrCreate(['name' => 'create students']);
        Permission::firstOrCreate(['name' => 'edit students']);
        Permission::firstOrCreate(['name' => 'delete students']);
        Permission::firstOrCreate(['name' => 'approve students']);

        // Teachers
        Permission::firstOrCreate(['name' => 'view teachers']);
        Permission::firstOrCreate(['name' => 'create teachers']);
        Permission::firstOrCreate(['name' => 'edit teachers']);
        Permission::firstOrCreate(['name' => 'delete teachers']);
        Permission::firstOrCreate(['name' => 'assign subjects to teacher']);

        // Enrollments
        Permission::firstOrCreate(['name' => 'manage enrollments']); // General enroll permission
        Permission::firstOrCreate(['name' => 'assign students to classroom']);

        // Finances
        Permission::firstOrCreate(['name' => 'manage fee installments']);
        Permission::firstOrCreate(['name' => 'manage fee payments']);

        // Exams & Schedules
        Permission::firstOrCreate(['name' => 'manage exams']);
        Permission::firstOrCreate(['name' => 'manage exam schedules']);

        // Settings (more granular as needed)
        Permission::firstOrCreate(['name' => 'manage schools']);
        Permission::firstOrCreate(['name' => 'manage academic years']);
        Permission::firstOrCreate(['name' => 'manage grade levels']);
        Permission::firstOrCreate(['name' => 'manage school-grade assignments']);
        Permission::firstOrCreate(['name' => 'manage subjects']);
        Permission::firstOrCreate(['name' => 'manage classrooms']);
        Permission::firstOrCreate(['name' => 'manage users']); // User management
        Permission::firstOrCreate(['name' => 'view settings dashboard']);

        // --- Define Roles ---

        // Admin Role (gets all permissions implicitly via Gate::before or explicitly)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        // Admins typically have all permissions. This can be handled by a Gate::before check
        // Or, assign all permissions:
        // $allPermissions = Permission::all();
        // $adminRole->syncPermissions($allPermissions);

        // Teacher Role
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $teacherRole->givePermissionTo([
            'view students', // Maybe only their students
            'manage exam schedules', // For their subjects/grades
            'assign subjects to teacher', // Maybe manage their own subjects
            // Add permissions to enter marks, take attendance later
        ]);

        // Student Role
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        // Students typically have very few direct "manage" permissions.
        // Their access is usually to view their own data.
        // Example: $studentRole->givePermissionTo(['view own profile', 'view own grades']);

        // Parent Role
        $parentRole = Role::firstOrCreate(['name' => 'parent']);
        // Parents also view data related to their children.
        // Example: $parentRole->givePermissionTo(['view child profile', 'view child grades']);

        // --- Assign Roles to Existing Users (Example) ---
        // Find your existing admin user and assign the 'admin' role
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
            // If you are keeping your 'role' column on users table, update it too:
            // $adminUser->update(['role' => 'admin']);
        }
    }
}