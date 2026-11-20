<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => \Illuminate\Support\Str::random(10),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'date_of_birth' => fake()->dateTimeBetween('-25 years', '-18 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'profile_photo' => null,
            'role_type' => fake()->randomElement(['admin', 'teacher', 'student', 'parent']),
            'is_active' => true,
        ];
    }

    public function admin(): static
    {
        return $this->state(['role_type' => 'admin']);
    }

    public function teacher(): static
    {
        return $this->state(['role_type' => 'teacher']);
    }

    public function student(): static
    {
        return $this->state(['role_type' => 'student']);
    }

    public function parent(): static
    {
        return $this->state(['role_type' => 'parent']);
    }
}