<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; // Import User model
use Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // === Define Permissions ===

        // General Admin Permissions
        Permission::firstOrCreate(['name' => 'manage system settings']);
        Permission::firstOrCreate(['name' => 'manage users']);
        Permission::firstOrCreate(['name' => 'manage roles']); // For managing roles & permissions themselves
        Permission::firstOrCreate(['name' => 'view system dashboard']); // Main dashboard

        // School Management
        Permission::firstOrCreate(['name' => 'create schools']);
        Permission::firstOrCreate(['name' => 'view any school']);    // For general manager
        Permission::firstOrCreate(['name' => 'view own school']);   // For principals
        Permission::firstOrCreate(['name' => 'edit any school']);
        Permission::firstOrCreate(['name' => 'edit own school']);
        Permission::firstOrCreate(['name' => 'delete schools']);
        Permission::firstOrCreate(['name' => 'manage school-grade_levels']); // Assign grades to school

        // Student Management
        Permission::firstOrCreate(['name' => 'view any student']);
        Permission::firstOrCreate(['name' => 'view own school students']);
        Permission::firstOrCreate(['name' => 'create students']);
        Permission::firstOrCreate(['name' => 'edit students']);
        Permission::firstOrCreate(['name' => 'delete students']);
        Permission::firstOrCreate(['name' => 'approve students']);
        Permission::firstOrCreate(['name' => 'print student profile']);

        // Teacher Management
        Permission::firstOrCreate(['name' => 'view any teacher']);
        Permission::firstOrCreate(['name' => 'view own school teachers']);
        Permission::firstOrCreate(['name' => 'create teachers']);
        Permission::firstOrCreate(['name' => 'edit teachers']);
        Permission::firstOrCreate(['name' => 'delete teachers']);
        Permission::firstOrCreate(['name' => 'assign subject to teacher']);

        // Academic Structure (beyond system settings)
        Permission::firstOrCreate(['name' => 'manage academic years']); // For a specific school
        Permission::firstOrCreate(['name' => 'manage grade levels']); // General grade levels
        Permission::firstOrCreate(['name' => 'manage subjects']);     // General subjects
        Permission::firstOrCreate(['name' => 'manage classrooms']);   // For a specific school/grade
        Permission::firstOrCreate(['name' => 'manage curriculum']);   // academic_year_subjects

        // Enrollments
        Permission::firstOrCreate(['name' => 'manage student enrollments']); // Assign student to year/grade/class
        Permission::firstOrCreate(['name' => 'view student enrollments']);

        // Exams & Results
        Permission::firstOrCreate(['name' => 'manage exams']); // Create exam periods
        Permission::firstOrCreate(['name' => 'manage exam schedules']);
        Permission::firstOrCreate(['name' => 'enter exam results']);
        Permission::firstOrCreate(['name' => 'view any exam results']);
        Permission::firstOrCreate(['name' => 'view own school exam results']);

        // Finances
        Permission::firstOrCreate(['name' => 'view student fee overview']); // General view of fees
        Permission::firstOrCreate(['name' => 'manage fee installments']); // Create/edit installments
        Permission::firstOrCreate(['name' => 'record fee payments']);     // Add/edit/delete payments
        Permission::firstOrCreate(['name' => 'view financial reports']); // Broader financial view
        Permission::firstOrCreate(['name' => 'access school treasury']);  // **Highly restricted**

        // Transport
        Permission::firstOrCreate(['name' => 'manage transport routes']);
        Permission::firstOrCreate(['name' => 'assign students to transport']);
        Permission::firstOrCreate(['name' => 'view transport assignments']);

        // Medical
        Permission::firstOrCreate(['name' => 'view student medical records']);
        Permission::firstOrCreate(['name' => 'edit student medical records']);


        // === Define Roles and Assign Permissions ===

        // 1. Super Manager Role
        $superManagerRole = Role::firstOrCreate(['name' => 'super-manager']);
        // Super managers get all permissions via Gate::before typically, or:
        // $superManagerRole->syncPermissions(Permission::all());

        // 2. General Manager Role
        $generalManagerRole = Role::firstOrCreate(['name' => 'general-manager']);
        $generalManagerRole->givePermissionTo([
            'view system dashboard',
            'manage system settings', // Can manage global academic years, grade levels, subjects
            'manage users',           // Can manage all users
            'manage roles',           // Can manage roles and permissions
            'create schools', 'view any school', 'edit any school', 'delete schools', 'manage school-grade_levels',
            'view any student', 'create students', 'edit students', 'delete students', 'approve students', 'print student profile',
            'view any teacher', 'create teachers', 'edit teachers', 'delete teachers', 'assign subject to teacher',
            'manage academic years', // Can manage specific school's years
            'manage classrooms',     // Can manage specific school's classrooms
            'manage curriculum',
            'manage student enrollments', 'view student enrollments',
            'manage exams', 'manage exam schedules', 'enter exam results', 'view any exam results',
            'view student fee overview', 'manage fee installments', 'record fee payments',
            // Does NOT get 'access school treasury'
            'view financial reports', // Can see reports but not raw treasury
            'manage transport routes', 'assign students to transport', 'view transport assignments',
            'view student medical records', 'edit student medical records',
        ]);

        // 3. Accountant Role
        $accountantRole = Role::firstOrCreate(['name' => 'accountant']);
        $accountantRole->givePermissionTo([
            'view any student', // To see their fee status
            'view student fee overview',
            'manage fee installments',
            'record fee payments',
            'view financial reports', // Specific to fees
        ]);

        // 4. School Principal Role (Generic - data access will be scoped by school_id on User model)
        $principalRole = Role::firstOrCreate(['name' => 'school-principal']);
        $principalRole->givePermissionTo([
            'view own school', 'edit own school', // Edit details of their school
            'manage school-grade_levels', // For their school
            'view own school students', 'create students', 'edit students', 'delete students', 'approve students', 'print student profile', // For their school
            'view own school teachers', 'create teachers', 'edit teachers', 'delete teachers', 'assign subject to teacher', // For their school
            'manage academic years', // For their school
            'manage classrooms',     // For their school
            'manage curriculum',     // For their school
            'manage student enrollments', 'view student enrollments', // For their school
            'manage exams', 'manage exam schedules', 'enter exam results', 'view own school exam results', // For their school
            'view student fee overview', // For students in their school
            'view transport assignments', // For students in their school
            'view student medical records', // For students in their school
        ]);

        // 5. Nurse Role
        $nurseRole = Role::firstOrCreate(['name' => 'nurse']);
        $nurseRole->givePermissionTo([
            'view student medical records',
            'edit student medical records',
            'view own school students', // To find students
        ]);

        // 6. Transport Manager Role
        $transportManagerRole = Role::firstOrCreate(['name' => 'transport-manager']);
        $transportManagerRole->givePermissionTo([
            'manage transport routes',
            'assign students to transport',
            'view transport assignments',
            'view any student', // To find students to assign to transport
        ]);


        // --- Create Users and Assign Roles (Example) ---
        // Ensure these users are created with your desired initial passwords
        // Super Managers
        $sm1 = User::firstOrCreate(['email' => 'supermanager1@example.com'], [
            'name' => 'Super Manager One', 'username' => 'supermanager1', 'password' => Hash::make('password'), 'role' => 'admin' // Main role column
        ]);
        $sm1->assignRole('super-manager'); // Spatie role

        $sm2 = User::firstOrCreate(['email' => 'supermanager2@example.com'], [
            'name' => 'Super Manager Two', 'username' => 'supermanager2', 'password' => Hash::make('password'), 'role' => 'admin'
        ]);
        $sm2->assignRole('super-manager');

        // General Manager
        $gm = User::firstOrCreate(['email' => 'gm@example.com'], [
            'name' => 'المدير العام', 'username' => 'generalmanager', 'password' => Hash::make('password'), 'role' => 'admin'
        ]);
        $gm->assignRole('general-manager');

        // Accountant
        $acc = User::firstOrCreate(['email' => 'accountant@example.com'], [
            'name' => 'المحاسب', 'username' => 'accountant', 'password' => Hash::make('password'), 'role' => 'teacher' // Or a custom 'staff' role
        ]);
        $acc->assignRole('accountant');

        // Transport Manager
        $tm = User::firstOrCreate(['email' => 'transport@example.com'], [
            'name' => 'مدير الترحيل', 'username' => 'transportmanager', 'password' => Hash::make('password'), 'role' => 'teacher' // Or 'staff'
        ]);
        $tm->assignRole('transport-manager');

        // Nurse
        $nurseUser = User::firstOrCreate(['email' => 'nurse@example.com'], [
            'name' => 'الممرضة', 'username' => 'nurse', 'password' => Hash::make('password'), 'role' => 'teacher' // Or 'staff'
        ]);
        $nurseUser->assignRole('nurse');

        // Principals - You'll need to associate them with specific schools manually or via another seeder
        // For now, we just create the user and assign the generic 'school-principal' Spatie role.
        // You would add a 'school_id' to the users table if a principal manages only one school.
        $principalBoysSec = User::firstOrCreate(['email' => 'principal.boys.sec@example.com'], [
            'name' => 'مدير الثانوية بنين', 'username' => 'principalboyssec', 'password' => Hash::make('password'), 'role' => 'teacher'
        ]);
        $principalBoysSec->assignRole('school-principal');
        // Add $principalBoysSec->school_id = X; $principalBoysSec->save(); if you have school_id on users.

        // ... create other principal users similarly ...
    }
}