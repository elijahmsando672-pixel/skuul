<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->admin()->create([
            'name' => 'System Administrator',
            'email' => 'admin@skuul.local',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        // Create teacher user
        $teacher = User::factory()->teacher()->create([
            'name' => 'John Teacher',
            'email' => 'teacher@skuul.local',
            'password' => bcrypt('password'),
        ]);
        $teacher->assignRole('teacher');

        // Create student user
        $student = User::factory()->student()->create([
            'name' => 'Jane Student',
            'email' => 'student@skuul.local',
            'password' => bcrypt('password'),
        ]);
        $student->assignRole('student');

        // Create parent user
        $parent = User::factory()->parent()->create([
            'name' => 'Bob Parent',
            'email' => 'parent@skuul.local',
            'password' => bcrypt('password'),
        ]);
        $parent->assignRole('parent');
    }
}