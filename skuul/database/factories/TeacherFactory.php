<?php

namespace Database\Factories;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->teacher(),
            'employee_id' => 'TCH' . fake()->unique()->numerify('######'),
            'department' => fake()->randomElement(['Mathematics', 'Science', 'English', 'History', 'Geography', 'Computer Science', 'Art', 'Music', 'Physical Education']),
            'hire_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'qualification' => fake()->randomElement(['Bachelor', 'Master', 'PhD']),
            'specialization' => fake()->sentence(),
        ];
    }
}