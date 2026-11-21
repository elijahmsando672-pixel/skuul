<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'teacher', 'student', 'parent'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $groups = [
            'users' => ['view', 'create', 'edit', 'delete'],
            'students' => ['view', 'create', 'edit', 'delete', 'enroll'],
            'teachers' => ['view', 'create', 'edit', 'delete'],
            'classrooms' => ['view', 'create', 'edit', 'delete'],
            'subjects' => ['view', 'create', 'edit', 'delete'],
            'academic years' => ['view', 'create', 'edit', 'delete'],
            'attendance' => ['view', 'take', 'edit'],
            'grades' => ['view', 'create', 'edit', 'delete'],
            'announcements' => ['view', 'create', 'edit', 'delete'],
            'timetables' => ['view', 'create', 'edit', 'delete'],
            'fees' => ['view', 'create', 'edit', 'delete'],
            'payments' => ['view', 'create', 'edit', 'delete'],
            'financial reports' => ['view'],
            'examinations' => ['view', 'create', 'edit', 'delete', 'publish'],
            'marks' => ['view', 'create', 'edit', 'delete'],
            'grading scales' => ['view', 'create', 'edit', 'delete'],
            'books' => ['view', 'create', 'edit', 'delete'],
            'book issues' => ['view', 'create', 'edit', 'delete'],
            'transport' => ['view', 'create', 'edit', 'delete'],
            'dashboard' => ['view'],
        ];

        $permissions = [];

        foreach ($groups as $subject => $actions) {
            foreach ($actions as $action) {
                $permissions[] = trim("{$action} {$subject}");
            }
        }

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        Role::findByName('admin', 'web')->syncPermissions($permissions);

        Role::findByName('teacher', 'web')->syncPermissions([
            'view dashboard', 'view students', 'view classrooms', 'view subjects',
            'view attendance', 'take attendance', 'edit attendance',
            'view grades', 'create grades', 'edit grades',
            'view timetables', 'view examinations', 'view marks', 'create marks', 'edit marks',
            'view books', 'view book issues', 'create book issues', 'edit book issues',
            'view transport',
        ]);

        Role::findByName('student', 'web')->syncPermissions([
            'view dashboard', 'view attendance', 'view grades', 'view timetables',
            'view fees', 'view payments', 'view examinations', 'view marks',
            'view books', 'view book issues', 'view transport',
        ]);

        Role::findByName('parent', 'web')->syncPermissions([
            'view dashboard', 'view attendance', 'view grades', 'view timetables',
            'view fees', 'view payments', 'view examinations', 'view marks', 'view transport',
        ]);
    }
}
