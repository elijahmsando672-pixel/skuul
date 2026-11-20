<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $teacherRole = Role::create(['name' => 'teacher', 'guard_name' => 'web']);
        $studentRole = Role::create(['name' => 'student', 'guard_name' => 'web']);
        $parentRole = Role::create(['name' => 'parent', 'guard_name' => 'web']);

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Student management
            'view students',
            'create students',
            'edit students',
            'delete students',
            'enroll students',

            // Teacher management
            'view teachers',
            'create teachers',
            'edit teachers',
            'delete teachers',

            // Classroom management
            'view classrooms',
            'create classrooms',
            'edit classrooms',
            'delete classrooms',

            // Subject management
            'view subjects',
            'create subjects',
            'edit subjects',
            'delete subjects',

            // Academic year management
            'view academic years',
            'create academic years',
            'edit academic years',
            'delete academic years',

            // Attendance
            'view attendance',
            'take attendance',
            'edit attendance',

            // Grades
            'view grades',
            'create grades',
            'edit grades',
            'delete grades',

            // Announcements
            'view announcements',
            'create announcements',
            'edit announcements',
            'delete announcements',

            // Dashboard
            'view dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to admin role
        $adminRole->givePermissionTo($permissions);

        // Assign permissions to teacher role
        $teacherRole->givePermissionTo([
            'view students',
            'view attendance',
            'take attendance',
            'edit attendance',
            'view grades',
            'create grades',
            'edit grades',
            'view dashboard',
        ]);

        // Assign permissions to student role
        $studentRole->givePermissionTo([
            'view dashboard',
            'view attendance',
            'view grades',
        ]);

        // Assign permissions to parent role
        $parentRole->givePermissionTo([
            'view dashboard',
            'view attendance',
            'view grades',
        ]);
    }
}