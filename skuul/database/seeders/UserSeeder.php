<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'System Administrator', 'email' => 'admin@skuul.local', 'role' => 'admin'],
            ['name' => 'John Teacher', 'email' => 'teacher@skuul.local', 'role' => 'teacher'],
            ['name' => 'Jane Student', 'email' => 'student@skuul.local', 'role' => 'student'],
            ['name' => 'Bob Parent', 'email' => 'parent@skuul.local', 'role' => 'parent'],
        ];

        foreach ($accounts as $account) {
            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => bcrypt('password'),
                    'role_type' => $account['role'],
                    'is_active' => true,
                ]
            );

            $user->syncRoles([$account['role']]);
        }
    }
}
